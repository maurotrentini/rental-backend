<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'property_id' => 'sometimes|required|exists:properties,id',
            'guest_id' => 'sometimes|required|exists:guests,id',
            'check_in_date' => 'sometimes|required|date',
            'check_out_date' => 'sometimes|required|date|after:check_in_date',
            'status' => 'sometimes|required|in:confirmed,cancelled,completed',
            'notes' => 'nullable|string',
            'extras' => 'nullable|array',
            'extras.*.extra_id' => 'required|exists:extras,id',
            'extras.*.quantity' => 'required|integer|min:1'
        ];
    }
}