<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExtraResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'quantity' => $this->when($this->pivot, function () {
                return $this->pivot->quantity;
            }),
            'unit_price' => $this->when($this->pivot, function () {
                return $this->pivot->unit_price;
            })
        ];
    }
}