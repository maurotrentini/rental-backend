<?php
namespace App\Services;

use App\Models\Booking;
use App\Models\Property;
use App\Models\Guest;
use App\Models\Extra;
use App\Mail\BookingConfirmation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingService
{
    public function createBooking(array $data): Booking
    {
        return DB::transaction(function () use ($data) {
            // Check availability
            $property = Property::findOrFail($data['property_id']);
            
            if (!$property->isAvailable($data['check_in_date'], $data['check_out_date'])) {
                throw new \Exception('Property is not available for the selected dates');
            }

            // Create or find guest
            $guest = Guest::firstOrCreate(
                ['email' => $data['guest_email']],
                [
                    'name' => $data['guest_name'],
                    'phone' => $data['guest_phone'] ?? null,
                    'notes' => $data['guest_notes'] ?? null
                ]
            );

            // Calculate total price
            $totalPrice = $this->calculatePrice($data);

            // Create booking
            $booking = Booking::create([
                'property_id' => $data['property_id'],
                'guest_id' => $guest->id,
                'check_in_date' => $data['check_in_date'],
                'check_out_date' => $data['check_out_date'],
                'total_price' => $totalPrice,
                'status' => 'confirmed'
            ]);

            // Attach extras if any
            if (!empty($data['extras'])) {
                $this->attachExtras($booking, $data['extras']);
            }

            // Send confirmation email
            $this->sendConfirmationEmail($booking);

            return $booking;
        });
    }

    public function updateBooking(Booking $booking, array $data): Booking
    {
        return DB::transaction(function () use ($booking, $data) {
            // Check availability for date changes
            if (isset($data['check_in_date']) || isset($data['check_out_date'])) {
                $checkIn = $data['check_in_date'] ?? $booking->check_in_date;
                $checkOut = $data['check_out_date'] ?? $booking->check_out_date;
                
                $property = $booking->property;
                $conflictingBooking = $property->bookings()
                    ->where('id', '!=', $booking->id)
                    ->where('status', '!=', 'cancelled')
                    ->where(function ($query) use ($checkIn, $checkOut) {
                        $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                            ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                            ->orWhere(function ($q) use ($checkIn, $checkOut) {
                                $q->where('check_in_date', '<=', $checkIn)
                                  ->where('check_out_date', '>=', $checkOut);
                            });
                    })
                    ->exists();

                if ($conflictingBooking) {
                    throw new \Exception('Property is not available for the selected dates');
                }
            }

            // Update booking fields
            $booking->update($data);

            // Update extras if provided
            if (isset($data['extras'])) {
                $booking->extras()->detach();
                $this->attachExtras($booking, $data['extras']);
            }

            // Recalculate total price
            $booking->update(['total_price' => $booking->calculateTotalPrice()]);

            return $booking;
        });
    }

    public function calculatePrice(array $data): float
    {
        $property = Property::findOrFail($data['property_id']);
        $nights = Carbon::parse($data['check_in_date'])->diffInDays($data['check_out_date']);
        $basePrice = $property->price_per_night * $nights;

        $extrasPrice = 0;
        if (!empty($data['extras'])) {
            foreach ($data['extras'] as $extraData) {
                $extra = Extra::find($extraData['id']);
                if ($extra) {
                    $extrasPrice += $extra->price * $extraData['quantity'];
                }
            }
        }

        return $basePrice + $extrasPrice;
    }

    private function attachExtras(Booking $booking, array $extras): void
    {
        foreach ($extras as $extraData) {
            $extra = Extra::find($extraData['id']);
            if ($extra) {
                $booking->extras()->attach($extra->id, [
                    'quantity' => $extraData['quantity'],
                    'price_at_booking' => $extra->price
                ]);
            }
        }
    }

    private function sendConfirmationEmail(Booking $booking): void
    {
        try {
            Mail::to($booking->guest->email)->send(new BookingConfirmation($booking));
        } catch (\Exception $e) {
            // Log error and fall back to console
            \Log::info("Booking confirmation email sent to console for booking {$booking->id}");
            \Log::info("Thanks for booking your stay from {$booking->check_in_date} to {$booking->check_out_date}!");
        }
    }
}