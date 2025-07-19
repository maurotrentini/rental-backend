<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'property_id' => 'required|exists:properties,id',
            'guest_id' => 'required|exists:guests,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'notes' => 'nullable|string',
            'extras' => 'nullable|array',
            'extras.*.id' => 'required|exists:extras,id',
            'extras.*.quantity' => 'required|integer|min:1'
        ];
    }

    public function messages()
    {
        return [
            'property_id.required' => 'Please select a property',
            'property_id.exists' => 'Selected property does not exist',
            'guest_id.required' => 'Please select a guest',
            'guest_id.exists' => 'Selected guest does not exist',
            'check_in_date.required' => 'Check-in date is required',
            'check_in_date.after_or_equal' => 'Check-in date cannot be in the past',
            'check_out_date.required' => 'Check-out date is required',
            'check_out_date.after' => 'Check-out date must be after check-in date',
            'extras.*.id.exists' => 'Selected extra does not exist',
            'extras.*.quantity.min' => 'Extra quantity must be at least 1'
        ];
    }
}