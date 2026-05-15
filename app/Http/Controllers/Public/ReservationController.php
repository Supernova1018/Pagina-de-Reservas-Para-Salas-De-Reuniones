<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Models\Reservation;
use App\Models\Space;
use App\Notifications\ReservationCreated;
use App\Notifications\ReservationStatusChanged;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReservationController extends Controller
{
    public function __construct(private AvailabilityService $availabilityService) {}

    /**
     * GET /reservations/new?space={slug}&start={datetime}
     */
    public function create(Request $request)
    {
        $space = null;
        $start = null;
        $end   = null;

        if ($request->filled('space')) {
            $space = Space::active()->where('slug', $request->space)->firstOrFail();
            $space->load('availabilities');
        }

        if ($request->filled('start')) {
            $start = Carbon::parse($request->start);
            $slotMinutes = (int) env('RESERVATION_SLOT_MINUTES', 60);
            $end = $start->copy()->addMinutes($slotMinutes);
        }

        return Inertia::render('Public/Reservations/Create', [
            'space'       => $space ? [
                'id'             => $space->id,
                'name'           => $space->name,
                'slug'           => $space->slug,
                'type'           => $space->type,
                'type_label'     => $space->getTypeLabel(),
                'capacity'       => $space->capacity,
                'price_per_hour' => $space->price_per_hour,
                'location'       => $space->location,
            ] : null,
            'startTime'   => $start?->toIso8601String(),
            'endTime'     => $end?->toIso8601String(),
            'spaces'      => Space::active()->get(['id', 'name', 'slug', 'type', 'capacity', 'price_per_hour', 'location']),
        ]);
    }

    /**
     * POST /reservations
     */
    public function store(StoreReservationRequest $request)
    {
        $space = Space::where('slug', $request->space_slug)->firstOrFail();
        $space->load('availabilities');

        $start = Carbon::parse($request->start_time);
        $end   = Carbon::parse($request->end_time);

        // Validate slot duration matches configured minimum
        $slotMinutes = (int) env('RESERVATION_SLOT_MINUTES', 60);
        $diff = $start->diffInMinutes($end);

        if ($diff < $slotMinutes) {
            return back()->withErrors([
                'start_time' => "La duración mínima de la reserva es {$slotMinutes} minutos.",
            ]);
        }

        // Backend availability check
        if (!$this->availabilityService->isSlotAvailable($space, $start, $end)) {
            return back()->withErrors([
                'start_time' => 'El horario seleccionado no está disponible. Por favor elige otro.',
            ]);
        }

        // Calculate total price
        $hours      = $diff / 60;
        $totalPrice = round($hours * $space->price_per_hour, 2);

        $reservation = Reservation::create([
            'space_id'     => $space->id,
            'user_id'      => auth()->id(),
            'start_time'   => $start,
            'end_time'     => $end,
            // If we reached this point the slot was validated as available,
            // create the reservation as confirmed so admin approval isn't required.
            'status'       => 'confirmed',
            'user_name'    => $request->user_name,
            'user_email'   => $request->user_email,
            'user_phone'   => $request->user_phone,
            'organization' => $request->organization,
            'notes'        => $request->notes,
            'total_price'  => $totalPrice,
        ]);

        // Send email notification
        $reservation->load('space');
        $reservation->notify(new ReservationCreated($reservation));

        

        return redirect()->route('public.reservations.confirmation', $reservation->slug)
            ->with('message', '¡Reserva confirmada!');
    }

    /**
     * GET /my-reservations
     */
    public function mine()
    {
        $user = auth()->user();

        $reservations = Reservation::with('space')
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('user_email', $user->email);
            })
            ->orderByDesc('start_time')
            ->get()
            ->map(fn ($reservation) => [
                'id' => $reservation->id,
                'slug' => $reservation->slug,
                'start_time' => $reservation->start_time->toIso8601String(),
                'end_time' => $reservation->end_time->toIso8601String(),
                'status' => $reservation->status,
                'status_label' => $reservation->status_label,
                'total_price' => $reservation->total_price,
                'space' => [
                    'name' => $reservation->space?->name,
                    'slug' => $reservation->space?->slug,
                    'location' => $reservation->space?->location,
                    'image' => $reservation->space?->image,
                ],
                'can_cancel' => $reservation->status === 'confirmed',
                'user_email' => $reservation->user_email,
            ]);

        return Inertia::render('Public/Reservations/Mine', [
            'reservations' => $reservations,
            'message' => session('message'),
        ]);
    }

    /**
     * POST /reservations/{reservation}/cancel
     */
    public function cancel(Reservation $reservation)
    {
        $user = auth()->user();

        if (! $user) {
            abort(403);
        }

        $ownsReservation = $reservation->user_id === $user->id || $reservation->user_email === $user->email;

        if (! $ownsReservation) {
            abort(403);
        }

        if (! $reservation->canTransitionTo('cancelled')) {
            return back()->withErrors([
                'status' => 'Esta reserva no puede ser cancelada.',
            ]);
        }

        $reservation->update(['status' => 'cancelled']);
        $reservation->load('space');
        $reservation->notify(new ReservationStatusChanged($reservation, 'cancelled'));

        return redirect()->route('public.reservations.mine')
            ->with('message', 'Reserva cancelada correctamente.');
    }

    /**
     * GET /reservations/{slug}/confirmation
     */
    public function confirmation(Reservation $reservation)
    {
        $reservation->load('space');

        return Inertia::render('Public/Reservations/Confirmation', [
            'reservation' => [
                'slug'         => $reservation->slug,
                'user_name'    => $reservation->user_name,
                'user_email'   => $reservation->user_email,
                'user_phone'   => $reservation->user_phone,
                'organization' => $reservation->organization,
                'notes'        => $reservation->notes,
                'start_time'   => $reservation->start_time->toIso8601String(),
                'end_time'     => $reservation->end_time->toIso8601String(),
                'status'       => $reservation->status,
                'status_label' => $reservation->status_label,
                'total_price'  => $reservation->total_price,
                'user_id'      => $reservation->user_id,
                'space'        => [
                    'name'     => $reservation->space->name,
                    'slug'     => $reservation->space->slug,
                    'location' => $reservation->space->location,
                    'image'    => $reservation->space->image,
                ],
            ],
            'message' => session('message'),
        ]);
    }
}