<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Space;
use App\Notifications\ReservationStatusChanged;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::with('space')->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('space')) {
            $query->whereHas('space', fn ($q) => $q->where('slug', $request->space));
        }

        if ($request->filled('date')) {
            $query->whereDate('start_time', $request->date);
        }

        $reservations = $query->paginate(15)->through(fn ($reservation) => $this->formatReservation($reservation));

        return Inertia::render('Admin/Reservations/Index', [
            'reservations' => $reservations,
            'spaces' => Space::active()->get(['id', 'name', 'slug']),
            'filters' => $request->only(['status', 'space', 'date']),
            'message' => session('message'),
        ]);
    }

    public function show(Reservation $reservation)
    {
        $reservation->load('space');

        return Inertia::render('Admin/Reservations/Show', [
            'reservation' => $this->formatReservation($reservation),
        ]);
    }

    public function accept(Reservation $reservation)
    {
        if (! $reservation->canTransitionTo('confirmed')) {
            return back()->withErrors(['status' => 'Esta reserva no puede ser confirmada.']);
        }

        $reservation->update(['status' => 'confirmed']);
        $reservation->load('space');
        $reservation->notify(new ReservationStatusChanged($reservation, 'confirmed'));

        return redirect()->route('admin.reservations.index')
            ->with('message', 'Reserva confirmada y notificación enviada.');
    }

    public function reject(Reservation $reservation)
    {
        if (! $reservation->canTransitionTo('rejected')) {
            return back()->withErrors(['status' => 'Esta reserva no puede ser rechazada.']);
        }

        $reservation->update(['status' => 'rejected']);
        $reservation->load('space');
        $reservation->notify(new ReservationStatusChanged($reservation, 'rejected'));

        return redirect()->route('admin.reservations.index')
            ->with('message', 'Reserva rechazada y notificación enviada.');
    }

    public function cancel(Reservation $reservation)
    {
        if (! $reservation->canTransitionTo('cancelled')) {
            return back()->withErrors(['status' => 'Esta reserva no puede ser cancelada.']);
        }

        $reservation->update(['status' => 'cancelled']);
        $reservation->load('space');
        $reservation->notify(new ReservationStatusChanged($reservation, 'cancelled'));

        return redirect()->route('admin.reservations.index')
            ->with('message', 'Reserva cancelada y notificación enviada.');
    }

    private function formatReservation(Reservation $reservation): array
    {
        return [
            'id' => $reservation->id,
            'slug' => $reservation->slug,
            'user_name' => $reservation->user_name,
            'user_email' => $reservation->user_email,
            'user_phone' => $reservation->user_phone,
            'organization' => $reservation->organization,
            'notes' => $reservation->notes,
            'start_time' => $reservation->start_time->toIso8601String(),
            'end_time' => $reservation->end_time->toIso8601String(),
            'status' => $reservation->status,
            'status_label' => $reservation->status_label,
            'total_price' => $reservation->total_price,
            'created_at' => $reservation->created_at->toIso8601String(),
            'space' => $reservation->space ? [
                'id' => $reservation->space->id,
                'name' => $reservation->space->name,
                'slug' => $reservation->space->slug,
            ] : null,
        ];
    }
}
