<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
            'price_per_night' => 'sometimes|required|numeric|min:0',
            'availability_calendar' => 'nullable|array',
            'is_active' => 'boolean'
        ];
    }
}