<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'space_slug' => 'required|exists:spaces,slug',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email|max:255',
            'user_phone' => 'nullable|string|max:30',
            'organization' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'status' => 'sometimes|in:pending,confirmed,rejected,cancelled,completed',
        ];
    }
}