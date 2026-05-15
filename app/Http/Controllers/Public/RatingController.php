<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RatingController extends Controller
{
    /**
     * POST /reservations/{slug}/rating
     * Store rating/review for a completed reservation
     */
    public function store(Reservation $reservation, Request $request)
    {
        // Only allow rating if reservation is completed
        if ($reservation->status !== 'completed') {
            return back()->withErrors([
                'error' => 'Solo puedes calificar reservas finalizadas.',
            ]);
        }

        // Check if already rated
        if ($reservation->rating) {
            return back()->withErrors([
                'error' => 'Esta reserva ya ha sido calificada.',
            ]);
        }

        $validated = $request->validate([
            'service_satisfaction' => ['required', 'integer', Rule::in([1, 2, 3, 4, 5])],
            'room_meets_expectations' => ['required', 'integer', Rule::in([1, 2, 3, 4, 5])],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        Rating::create([
            'reservation_id' => $reservation->id,
            'user_id' => auth()->id(),
            'service_satisfaction' => $validated['service_satisfaction'],
            'room_meets_expectations' => $validated['room_meets_expectations'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return redirect()->route('public.reservations.confirmation', $reservation->slug)
            ->with('message', '¡Gracias por tu calificación!');
    }
}
