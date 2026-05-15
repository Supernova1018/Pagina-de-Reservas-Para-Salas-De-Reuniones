<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSpaceRequest;
use App\Http\Requests\UpdateSpaceRequest;
use App\Models\Space;

class SpaceController extends Controller
{
    // GET /api/spaces
    public function index()
    {
        $spaces = Space::withCount('reservations')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Space $space) => $this->formatSpace($space));

        return response()->json([
            'spaces' => $spaces,
        ]);
    }

    // GET /api/spaces/{space}
    public function show(Space $space)
    {
        $space->load('availabilities');
        $space->loadCount('reservations');

        return response()->json([
            'space' => $this->formatSpace($space, true),
        ]);
    }

    // POST /api/spaces
    public function store(StoreSpaceRequest $request)
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);

        $space = Space::create($validated);
        $space->loadCount('reservations');

        return response()->json([
            'message' => 'Sala creada correctamente.',
            'space' => $this->formatSpace($space),
        ], 201);
    }

    // PUT/PATCH /api/spaces/{space}
    public function update(UpdateSpaceRequest $request, Space $space)
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);

        $space->update($validated);
        $space->load('availabilities');
        $space->loadCount('reservations');

        return response()->json([
            'message' => 'Sala actualizada correctamente.',
            'space' => $this->formatSpace($space, true),
        ]);
    }

    // DELETE /api/spaces/{space}
    public function destroy(Space $space)
    {
        $space->delete();

        return response()->json([
            'message' => 'Sala eliminada correctamente.',
        ]);
    }

    // GET /api/spaces/{space}/availability
    public function availability(Space $space)
    {
        return response()->json([
            'space' => $space->name,
            'available' => true
        ]);
    }

    private function formatSpace(Space $space, bool $includeAvailabilities = false): array
    {
        $formatted = [
            'id' => $space->id,
            'name' => $space->name,
            'slug' => $space->slug,
            'type' => $space->type,
            'type_label' => $space->getTypeLabel(),
            'capacity' => $space->capacity,
            'description' => $space->description,
            'rules' => $space->rules,
            'price_per_hour' => $space->price_per_hour,
            'is_active' => $space->is_active,
            'location' => $space->location,
            'image' => $space->image,
            'reservations_count' => $space->reservations_count ?? 0,
        ];

        if ($includeAvailabilities) {
            $formatted['availabilities'] = $space->availabilities->map(fn ($availability) => [
                'id' => $availability->id,
                'day_of_week' => $availability->day_of_week,
                'day_name' => $availability->day_name,
                'start_time' => $availability->start_time,
                'end_time' => $availability->end_time,
            ]);
        }

        return $formatted;
    }
}