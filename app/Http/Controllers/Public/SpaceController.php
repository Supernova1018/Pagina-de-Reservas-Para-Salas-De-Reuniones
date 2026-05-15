<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Space;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SpaceController extends Controller
{
    public function __construct(private AvailabilityService $availabilityService) {}

    /**
     * GET / — Public listing with filters
     */
    public function index(Request $request)
    {
        $query = Space::active()->with('availabilities');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('capacity')) {
            $query->where('capacity', '>=', $request->capacity);
        }

        $spaces = $query->get()->map(fn ($s) => [
            'id'            => $s->id,
            'name'          => $s->name,
            'slug'          => $s->slug,
            'type'          => $s->type,
            'type_label'    => $s->getTypeLabel(),
            'capacity'      => $s->capacity,
            'description'   => $s->description,
            'price_per_hour'=> $s->price_per_hour,
            'location'      => $s->location,
            'image'         => $s->image,
        ]);

        return Inertia::render('Public/Spaces/Index', [
            'spaces'  => $spaces,
            'filters' => $request->only(['type', 'capacity']),
        ]);
    }

    /**
     * GET /spaces/{slug} — Space detail + next available slots
     */
    public function show(Space $space)
    {
        $space->load('availabilities');

        $nextSlots = $this->availabilityService->getNextAvailableSlots($space, 12);

        return Inertia::render('Public/Spaces/Show', [
            'space'      => [
                'id'            => $space->id,
                'name'          => $space->name,
                'slug'          => $space->slug,
                'type'          => $space->type,
                'type_label'    => $space->getTypeLabel(),
                'capacity'      => $space->capacity,
                'description'   => $space->description,
                'rules'         => $space->rules,
                'price_per_hour'=> $space->price_per_hour,
                'location'      => $space->location,
                'image'         => $space->image,
                'availabilities'=> $space->availabilities,
            ],
            'nextSlots'  => $nextSlots,
        ]);
    }

    /**
     * GET /spaces/{slug}/availability?date=YYYY-MM-DD — JSON for a given date
     */
    public function availability(Space $space, Request $request)
    {
        $date  = $request->filled('date') ? Carbon::parse($request->date) : Carbon::today();
        $from  = $date->copy()->startOfDay();
        $to    = $date->copy()->endOfDay();

        $slots = $this->availabilityService->getAvailableSlots($space, $from, $to);

        return response()->json(['slots' => $slots]);
    }
}