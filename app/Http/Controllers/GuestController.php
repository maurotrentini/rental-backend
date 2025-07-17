<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Http\Requests\StoreGuestRequest;
use App\Http\Requests\UpdateGuestRequest;
use App\Http\Resources\GuestResource;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $query = Guest::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $guests = $query->orderBy('created_at', 'desc')->paginate(15);

        return GuestResource::collection($guests);
    }

    public function store(StoreGuestRequest $request)
    {
        $guest = Guest::create($request->validated());

        return new GuestResource($guest);
    }

    public function show(Guest $guest)
    {
        return new GuestResource($guest->load('bookings.property'));
    }

    public function update(UpdateGuestRequest $request, Guest $guest)
    {
        $guest->update($request->validated());

        return new GuestResource($guest);
    }

    public function destroy(Guest $guest)
    {
        // Check if guest has active bookings
        if ($guest->bookings()->active()->exists()) {
            return response()->json([
                'message' => 'Cannot delete guest with active bookings'
            ], 422);
        }

        $guest->delete();

        return response()->json(['message' => 'Guest deleted successfully']);
    }

    public function bookingHistory(Guest $guest)
    {
        $bookings = $guest->bookings()
            ->with(['property', 'extras'])
            ->orderBy('check_in_date', 'desc')
            ->get();

        return response()->json($bookings);
    }
}