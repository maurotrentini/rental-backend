<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'description' => 'required|string',
            'price_per_night' => 'required|numeric|min:0',
            'availability_calendar' => 'nullable|array',
            'is_active' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Property name is required',
            'address.required' => 'Property address is required',
            'description.required' => 'Property description is required',
            'price_per_night.required' => 'Price per night is required',
            'price_per_night.numeric' => 'Price per night must be a valid number',
            'price_per_night.min' => 'Price per night cannot be negative'
        ];
    }
}