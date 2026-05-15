<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSpaceRequest;
use App\Http\Requests\UpdateSpaceRequest;
use App\Models\Availability;
use App\Models\Space;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class SpaceController extends Controller
{
    public function index()
    {
        $spaces = Space::withCount('reservations')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($s) => [
                'id'                 => $s->id,
                'name'               => $s->name,
                'slug'               => $s->slug,
                'type'               => $s->type,
                'type_label'         => $s->getTypeLabel(),
                'capacity'           => $s->capacity,
                'price_per_hour'     => $s->price_per_hour,
                'is_active'          => $s->is_active,
                'location'           => $s->location,
                'reservations_count' => $s->reservations_count,
            ]);

        return Inertia::render('Admin/Spaces/Index', [
            'spaces'  => $spaces,
            'message' => session('message'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Spaces/Create');
    }

    public function store(StoreSpaceRequest $request)
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);

        $space = Space::create($validated);

        // Store weekly availability
        $this->syncAvailabilities($space, $request->input('availabilities', []));

        return redirect()->route('admin.spaces.index')
            ->with('message', 'Sala creada exitosamente.');
    }

    public function show(Space $space)
    {
        $space->load(['availabilities', 'reservations.space']);

        return Inertia::render('Admin/Spaces/Show', [
            'space' => $this->formatSpace($space),
        ]);
    }

    public function edit(Space $space)
    {
        $space->load('availabilities');

        return Inertia::render('Admin/Spaces/Edit', [
            'space' => $this->formatSpace($space),
        ]);
    }

    public function update(UpdateSpaceRequest $request, Space $space)
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);

        $space->update($validated);

        $this->syncAvailabilities($space, $request->input('availabilities', []));

        return redirect()->route('admin.spaces.index')
            ->with('message', 'Sala actualizada exitosamente.');
    }

    public function destroy(Space $space)
    {
        $space->delete();

        return redirect()->route('admin.spaces.index')
            ->with('message', 'Sala eliminada exitosamente.');
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function syncAvailabilities(Space $space, array $availabilities): void
    {
        $space->availabilities()->delete();

        foreach ($availabilities as $avail) {
            if (!empty($avail['enabled']) && !empty($avail['start_time']) && !empty($avail['end_time'])) {
                $space->availabilities()->create([
                    'day_of_week' => $avail['day_of_week'],
                    'start_time'  => $avail['start_time'],
                    'end_time'    => $avail['end_time'],
                ]);
            }
        }
    }

    private function formatSpace(Space $space): array
    {
        return [
            'id'             => $space->id,
            'name'           => $space->name,
            'slug'           => $space->slug,
            'type'           => $space->type,
            'type_label'     => $space->getTypeLabel(),
            'capacity'       => $space->capacity,
            'description'    => $space->description,
            'rules'          => $space->rules,
            'price_per_hour' => $space->price_per_hour,
            'is_active'      => $space->is_active,
            'location'       => $space->location,
            'image'          => $space->image,
            'availabilities' => $space->availabilities->map(fn ($a) => [
                'id'          => $a->id,
                'day_of_week' => $a->day_of_week,
                'day_name'    => $a->day_name,
                'start_time'  => $a->start_time,
                'end_time'    => $a->end_time,
            ]),
        ];
    }
}