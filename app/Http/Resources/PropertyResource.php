<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'description' => $this->description,
            'price_per_night' => $this->price_per_night,
            'availability_calendar' => $this->availability_calendar,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'bookings' => BookingResource::collection($this->whenLoaded('bookings')),
            'active_bookings_count' => $this->when(
                $this->relationLoaded('bookings'),
                function () {
                    return $this->bookings->where('status', 'confirmed')->count();
                }
            )
        ];
    }
}