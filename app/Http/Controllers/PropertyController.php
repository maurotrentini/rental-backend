<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Http\Resources\PropertyResource;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $properties = $query->orderBy('created_at', 'desc')->paginate(15);

        return PropertyResource::collection($properties);
    }

    public function store(StorePropertyRequest $request)
    {
        $property = Property::create($request->validated());

        return new PropertyResource($property);
    }

    public function show(Property $property)
    {
        return new PropertyResource($property->load('bookings.guest'));
    }

    public function update(UpdatePropertyRequest $request, Property $property)
    {
        $property->update($request->validated());

        return new PropertyResource($property);
    }

    public function destroy(Property $property)
    {
        // Check if property has active bookings
        if ($property->activeBookings()->exists()) {
            return response()->json([
                'message' => 'Cannot delete property with active bookings'
            ], 422);
        }

        $property->delete();

        return response()->json(['message' => 'Property deleted successfully']);
    }

    public function availability(Property $property, Request $request)
    {
        $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in'
        ]);

        $isAvailable = $property->isAvailable(
            $request->check_in,
            $request->check_out
        );

        return response()->json([
            'available' => $isAvailable,
            'property_id' => $property->id,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out
        ]);
    }
}