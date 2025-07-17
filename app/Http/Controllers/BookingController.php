<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Services\BookingService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $bookingService;
    protected $notificationService;

    public function __construct(BookingService $bookingService, NotificationService $notificationService)
    {
        $this->bookingService = $bookingService;
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $query = Booking::with(['property', 'guest', 'extras']);

        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->has('property_id')) {
            $query->where('property_id', $request->get('property_id'));
        }

        if ($request->has('guest_id')) {
            $query->where('guest_id', $request->get('guest_id'));
        }

        if ($request->has('date_from')) {
            $query->where('check_in_date', '>=', $request->get('date_from'));
        }

        if ($request->has('date_to')) {
            $query->where('check_out_date', '<=', $request->get('date_to'));
        }

        $bookings = $query->orderBy('check_in_date', 'desc')->paginate(15);

        return BookingResource::collection($bookings);
    }

    public function store(StoreBookingRequest $request)
    {
        try {
            $booking = $this->bookingService->createBooking($request->validated());
            
            // Send notification
            $this->notificationService->sendBookingConfirmation($booking);
            
            return new BookingResource($booking);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create booking',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function show(Booking $booking)
    {
        return new BookingResource($booking->load(['property', 'guest', 'extras']));
    }

    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        try {
            $updatedBooking = $this->bookingService->updateBooking($booking, $request->validated());
            
            return new BookingResource($updatedBooking);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update booking',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy(Booking $booking)
    {
        $booking->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Booking cancelled successfully']);
    }

    public function calendar(Request $request)
    {
        $bookings = Booking::with(['property', 'guest'])
            ->active()
            ->when($request->has('property_id'), function ($query) use ($request) {
                $query->where('property_id', $request->get('property_id'));
            })
            ->get();

        $events = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'title' => $booking->guest->name . ' - ' . $booking->property->name,
                'start' => $booking->check_in_date->format('Y-m-d'),
                'end' => $booking->check_out_date->format('Y-m-d'),
                'backgroundColor' => '#3B82F6',
                'borderColor' => '#1D4ED8',
                'extendedProps' => [
                    'booking_id' => $booking->id,
                    'guest_name' => $booking->guest->name,
                    'property_name' => $booking->property->name,
                    'total_price' => $booking->total_price
                ]
            ];
        });

        return response()->json($events);
    }
}