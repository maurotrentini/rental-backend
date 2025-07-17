<?php

namespace App\Services;

use App\Models\Booking;
use App\Mail\BookingConfirmation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function sendBookingConfirmation(Booking $booking)
    {
        try {
            Mail::to($booking->guest->email)->send(new BookingConfirmation($booking));
            
            Log::info('Booking confirmation email sent', [
                'booking_id' => $booking->id,
                'guest_email' => $booking->guest->email
            ]);
        } catch (\Exception $e) {
            // Fallback to console logging
            Log::info('Booking Confirmation (Email Failed)', [
                'booking_id' => $booking->id,
                'guest_name' => $booking->guest->name,
                'guest_email' => $booking->guest->email,
                'property_name' => $booking->property->name,
                'check_in' => $booking->check_in_date->format('Y-m-d'),
                'check_out' => $booking->check_out_date->format('Y-m-d'),
                'total_price' => $booking->total_price,
                'message' => "Thanks for booking your stay from {$booking->check_in_date->format('M j, Y')} to {$booking->check_out_date->format('M j, Y')}!",
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function sendBookingUpdate(Booking $booking, string $updateType = 'updated')
    {
        try {
            // You can create additional mail classes for different notification types
            Log::info('Booking ' . $updateType, [
                'booking_id' => $booking->id,
                'guest_email' => $booking->guest->email,
                'status' => $booking->status
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send booking update notification', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}