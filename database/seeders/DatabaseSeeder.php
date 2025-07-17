<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Property;
use App\Models\Guest;
use App\Models\Extra;
use App\Models\Booking;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@rental.com',
            'password' => bcrypt('password')
        ]);

        // Create sample properties
        $properties = [
            [
                'name' => 'Cozy Downtown Apartment',
                'address' => '123 Main St, Downtown, City 12345',
                'description' => 'A beautiful 2-bedroom apartment in the heart of downtown with modern amenities and stunning city views.',
                'price_per_night' => 120.00
            ],
            [
                'name' => 'Beachfront Villa',
                'address' => '456 Ocean Drive, Beachside, City 67890',
                'description' => 'Luxurious 4-bedroom villa with direct beach access, private pool, and panoramic ocean views.',
                'price_per_night' => 350.00
            ],
            [
                'name' => 'Mountain Cabin Retreat',
                'address' => '789 Pine Ridge, Mountain View, City 54321',
                'description' => 'Rustic 3-bedroom cabin surrounded by nature, perfect for a peaceful getaway with hiking trails nearby.',
                'price_per_night' => 180.00
            ]
        ];

        foreach ($properties as $propertyData) {
            Property::create($propertyData);
        }

        // Create sample guests
        $guests = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@email.com',
                'phone' => '+1-555-0123',
                'notes' => 'Prefers ground floor rooms'
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@email.com',
                'phone' => '+1-555-0456',
                'notes' => 'Traveling with small dog'
            ],
            [
                'name' => 'Mike Davis',
                'email' => 'mike.davis@email.com',
                'phone' => '+1-555-0789',
                'notes' => 'Business traveler, needs early check-in'
            ]
        ];

        foreach ($guests as $guestData) {
            Guest::create($guestData);
        }

        // Create sample extras
        $extras = [
            [
                'name' => 'Airport Pickup',
                'description' => 'Private car service from airport to property',
                'price' => 45.00
            ],
            [
                'name' => 'Late Checkout',
                'description' => 'Extend checkout time until 2 PM',
                'price' => 25.00
            ],
            [
                'name' => 'Welcome Basket',
                'description' => 'Local treats and beverages upon arrival',
                'price' => 35.00
            ],
            [
                'name' => 'Extra Cleaning',
                'description' => 'Deep cleaning service during stay',
                'price' => 75.00
            ],
            [
                'name' => 'Pet Fee',
                'description' => 'Additional fee for pets',
                'price' => 20.00
            ]
        ];

        foreach ($extras as $extraData) {
            Extra::create($extraData);
        }

        // Create sample bookings
        $bookings = [
            [
                'property_id' => 1,
                'guest_id' => 1,
                'check_in_date' => now()->addDays(7),
                'check_out_date' => now()->addDays(10),
                'status' => 'confirmed'
            ],
            [
                'property_id' => 2,
                'guest_id' => 2,
                'check_in_date' => now()->addDays(14),
                'check_out_date' => now()->addDays(21),
                'status' => 'confirmed'
            ],
            [
                'property_id' => 3,
                'guest_id' => 3,
                'check_in_date' => now()->addDays(3),
                'check_out_date' => now()->addDays(5),
                'status' => 'confirmed'
            ]
        ];

        foreach ($bookings as $bookingData) {
            $property = Property::find($bookingData['property_id']);
            $checkIn = $bookingData['check_in_date'];
            $checkOut = $bookingData['check_out_date'];
            $nights = $checkIn->diffInDays($checkOut);
            
            $booking = Booking::create([
                ...$bookingData,
                'total_price' => $property->price_per_night * $nights
            ]);

            // Add some extras to bookings
            if ($booking->id === 1) {
                $booking->extras()->attach(1, ['quantity' => 1, 'price_at_booking' => 45.00]); // Airport Pickup
                $booking->extras()->attach(3, ['quantity' => 1, 'price_at_booking' => 35.00]); // Welcome Basket
                $booking->update(['total_price' => $booking->total_price + 80.00]);
            }

            if ($booking->id === 2) {
                $booking->extras()->attach(2, ['quantity' => 1, 'price_at_booking' => 25.00]); // Late Checkout
                $booking->extras()->attach(5, ['quantity' => 1, 'price_at_booking' => 20.00]); // Pet Fee
                $booking->update(['total_price' => $booking->total_price + 45.00]);
            }
        }
    }
}