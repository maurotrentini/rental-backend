<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'property_id' => $this->property_id,
            'guest_id' => $this->guest_id,
            'check_in_date' => $this->check_in_date->format('Y-m-d'),
            'check_out_date' => $this->check_out_date->format('Y-m-d'),
            'total_price' => $this->total_price,
            'status' => $this->status,
            'notes' => $this->notes,
            'nights' => $this->nights,
            'base_amount' => $this->base_amount,
            'extras_amount' => $this->extras_amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'property' => new PropertyResource($this->whenLoaded('property')),
            'guest' => new GuestResource($this->whenLoaded('guest')),
            'extras' => ExtraResource::collection($this->whenLoaded('extras'))
        ];
    }
}