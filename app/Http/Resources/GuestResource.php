<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GuestResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'bookings' => BookingResource::collection($this->whenLoaded('bookings')),
            'total_bookings' => $this->when(
                $this->relationLoaded('bookings'),
                function () {
                    return $this->bookings->count();
                }
            )
        ];
    }
}