<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Reservation;
use App\Models\Space;
use App\Services\AvailabilityService;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function __construct(
        private AvailabilityService $availabilityService
    ) {}

    public function index()
    {
        $reservations = Reservation::with('space')
            ->orderByDesc('start_time')
            ->get()
            ->map(fn (Reservation $reservation) => $this->formatReservation($reservation));

        return response()->json([
            'reservations' => $reservations,
        ]);
    }

    public function show(Reservation $reservation)
    {
        $reservation->load('space');

        return response()->json([
            'reservation' => $this->formatReservation($reservation),
        ]);
    }

    public function store(StoreReservationRequest $request)
    {
        $validated = $request->validated();
        $space = Space::where('slug', $validated['space_slug'])->firstOrFail();

        $start = Carbon::parse($validated['start_time']);
        $end = Carbon::parse($validated['end_time']);

        // Validar disponibilidad
        if (!$this->availabilityService->isSlotAvailable($space, $start, $end)) {
            return response()->json([
                'message' => 'El horario no está disponible.',
            ], 422);
        }

        // Calcular precio
        $hours = $start->diffInMinutes($end) / 60;
        $totalPrice = round($hours * $space->price_per_hour, 2);

        // Crear reserva
        $reservation = Reservation::create([
            'space_id'     => $space->id,
            'start_time'   => $start,
            'end_time'     => $end,
            'status'       => 'pending',
            'user_name'    => $validated['user_name'],
            'user_email'   => $validated['user_email'],
            'user_phone'   => $validated['user_phone'] ?? null,
            'organization' => $validated['organization'] ?? null,
            'notes'        => $validated['notes'] ?? null,
            'total_price'  => $totalPrice,
        ]);

        $reservation->load('space');

        return response()->json([
            'message' => 'Reserva creada correctamente',
            'reservation' => $this->formatReservation($reservation),
        ], 201);
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        $validated = $request->validated();
        $space = Space::where('slug', $validated['space_slug'])->firstOrFail();

        $start = Carbon::parse($validated['start_time']);
        $end = Carbon::parse($validated['end_time']);

        if (!$this->availabilityService->isSlotAvailable($space, $start, $end, $reservation->id)) {
            return response()->json([
                'message' => 'El horario no está disponible.',
            ], 422);
        }

        $hours = $start->diffInMinutes($end) / 60;
        $totalPrice = round($hours * $space->price_per_hour, 2);

        $reservation->update([
            'space_id' => $space->id,
            'start_time' => $start,
            'end_time' => $end,
            'status' => $validated['status'] ?? $reservation->status,
            'user_name' => $validated['user_name'],
            'user_email' => $validated['user_email'],
            'user_phone' => $validated['user_phone'] ?? null,
            'organization' => $validated['organization'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'total_price' => $totalPrice,
        ]);

        $reservation->load('space');

        return response()->json([
            'message' => 'Reserva actualizada correctamente.',
            'reservation' => $this->formatReservation($reservation),
        ]);
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return response()->json([
            'message' => 'Reserva eliminada correctamente.',
        ]);
    }

    public function confirmation(Reservation $reservation)
    {
        $reservation->load('space');

        return response()->json([
            'reservation' => $this->formatReservation($reservation),
        ]);
    }

    private function formatReservation(Reservation $reservation): array
    {
        $reservation->loadMissing('space');

        return [
            'id' => $reservation->id,
            'slug' => $reservation->slug,
            'status' => $reservation->status,
            'status_label' => $reservation->status_label,
            'user_name' => $reservation->user_name,
            'user_email' => $reservation->user_email,
            'user_phone' => $reservation->user_phone,
            'organization' => $reservation->organization,
            'notes' => $reservation->notes,
            'start_time' => $reservation->start_time,
            'end_time' => $reservation->end_time,
            'total_price' => $reservation->total_price,
            'duration_hours' => $reservation->duration_in_hours,
            'space' => [
                'id' => $reservation->space?->id,
                'name' => $reservation->space?->name,
                'slug' => $reservation->space?->slug,
                'location' => $reservation->space?->location,
                'price_per_hour' => $reservation->space?->price_per_hour,
            ],
        ];
    }
}