<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Space;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarController extends Controller
{
    public function __construct(private AvailabilityService $availabilityService) {}

    public function index(Request $request)
    {
        $spaces = Space::active()->get(['id', 'name', 'slug']);

        $selectedSpaceSlug = $request->input('space', $spaces->first()?->slug);
        $space = Space::where('slug', $selectedSpaceSlug)->with('availabilities')->first();

        // Week navigation
        $weekOffset = (int) $request->input('week', 0);
        $weekStart  = Carbon::now()->startOfWeek(Carbon::MONDAY)->addWeeks($weekOffset);

        $calendar = [];
        if ($space) {
            $calendar = $this->availabilityService->getWeekCalendar($space, $weekStart);
        }

        return Inertia::render('Admin/Calendar', [
            'spaces'        => $spaces,
            'selectedSpace' => $selectedSpaceSlug,
            'weekStart'     => $weekStart->toDateString(),
            'weekOffset'    => $weekOffset,
            'calendar'      => $calendar,
        ]);
    }
}