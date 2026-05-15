<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $minimumLeadMinutes = (int) env('RESERVATION_MIN_LEAD_MINUTES', 240);
        $minimumLeadTime = Carbon::now(config('app.timezone'))->addMinutes($minimumLeadMinutes)->toDateTimeString();

        return [
            'space_slug'   => 'required|exists:spaces,slug',
            'start_time'   => 'required|date|after:' . $minimumLeadTime,
            'end_time'     => 'required|date|after:start_time',
            'user_name'    => 'required|string|max:255',
            'user_email'   => 'required|email|max:255',
            'user_phone'   => 'nullable|string|max:30',
            'organization' => 'nullable|string|max:255',
            'notes'        => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'space_slug.required'  => 'Debes seleccionar una sala.',
            'space_slug.exists'    => 'La sala seleccionada no existe.',
            'start_time.required'  => 'La hora de inicio es obligatoria.',
            'start_time.after'     => 'La reserva debe hacerse con al menos ' . (int) (env('RESERVATION_MIN_LEAD_MINUTES', 240) / 60) . ' horas de anticipación.',
            'end_time.required'    => 'La hora de fin es obligatoria.',
            'end_time.after'       => 'La hora de fin debe ser posterior al inicio.',
            'user_name.required'   => 'Tu nombre es obligatorio.',
            'user_email.required'  => 'Tu correo electrónico es obligatorio.',
            'user_email.email'     => 'El correo electrónico no es válido.',
        ];
    }
}