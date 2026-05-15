<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Reservation;
use App\Models\Space;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $stats = [
            'total_spaces'     => Space::count(),
            'active_spaces'    => Space::active()->count(),
            'confirmed_count'  => Reservation::confirmed()->count(),
            'cancelled_count'  => Reservation::where('status', 'cancelled')->count(),
            'today_count'      => Reservation::where('status', 'confirmed')->whereDate('start_time', $today)->count(),
            'this_month_count' => Reservation::where('status', 'confirmed')->whereMonth('start_time', $today->month)->count(),
            'revenue_total'    => Reservation::whereIn('status', ['confirmed', 'completed'])->sum('total_price'),
            'ratings_count'    => Rating::count(),
        ];

        $topSpaces = Space::query()
            ->withCount(['reservations as confirmed_reservations_count' => fn ($query) => $query->whereIn('status', ['confirmed', 'completed'])])
            ->orderByDesc('confirmed_reservations_count')
            ->limit(6)
            ->get()
            ->map(fn ($space) => [
                'id' => $space->id,
                'name' => $space->name,
                'slug' => $space->slug,
                'location' => $space->location,
                'image' => $space->image,
                'capacity' => $space->capacity,
                'price_per_hour' => $space->price_per_hour,
                'confirmed_reservations_count' => (int) $space->confirmed_reservations_count,
            ]);

        $recentReservations = Reservation::with('space')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get()
            ->map(fn ($r) => [
                'id'           => $r->id,
                'slug'         => $r->slug,
                'user_name'    => $r->user_name,
                'user_email'   => $r->user_email,
                'start_time'   => $r->start_time->toIso8601String(),
                'end_time'     => $r->end_time->toIso8601String(),
                'status'       => $r->status,
                'status_label' => $r->status_label,
                'space_name'   => $r->space?->name,
                'space_slug'   => $r->space?->slug,
            ]);

        $upcomingToday = Reservation::with('space')
            ->where('status', 'confirmed')
            ->whereDate('start_time', $today)
            ->orderBy('start_time')
            ->get()
            ->map(fn ($r) => [
                'id'         => $r->id,
                'slug'       => $r->slug,
                'user_name'  => $r->user_name,
                'start_time' => $r->start_time->toIso8601String(),
                'end_time'   => $r->end_time->toIso8601String(),
                'status'     => $r->status,
                'space_name' => $r->space?->name,
            ]);

        return Inertia::render('Admin/Dashboard', [
            'stats'              => $stats,
            'recentReservations' => $recentReservations,
            'upcomingToday'      => $upcomingToday,
            'topSpaces'          => $topSpaces,
        ]);
    }
}