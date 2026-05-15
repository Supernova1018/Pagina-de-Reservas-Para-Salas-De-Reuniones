<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_satisfactory'    => ['required', 'boolean'],
            'space_met_expectations'  => ['required', 'boolean'],
            'overall_score'           => ['required', 'integer', 'min:1', 'max:5'],
            'comment'                 => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'service_satisfactory.required' => 'Indica si el servicio de reserva fue satisfactorio.',
            'space_met_expectations.required' => 'Indica si la sala cumplió tus expectativas.',
            'overall_score.required' => 'Selecciona una calificación general.',
            'overall_score.min' => 'La calificación general debe ser al menos 1.',
            'overall_score.max' => 'La calificación general no puede superar 5.',
        ];
    }
}