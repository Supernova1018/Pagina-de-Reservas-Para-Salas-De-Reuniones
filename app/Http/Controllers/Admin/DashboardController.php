<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Space;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $timezone = config('app.timezone');
        $today = Carbon::today($timezone);
        $confirmedStatuses = ['confirmed', 'completed'];
        $historyStart = Carbon::now($timezone)->subDays(90)->startOfDay();

        $confirmedReservations = Reservation::query()->whereIn('status', $confirmedStatuses);

        $historicalReservations = Reservation::query()
            ->whereIn('status', $confirmedStatuses)
            ->where('start_time', '>=', $historyStart)
            ->get(['start_time']);

        $timeSlots = collect([
            ['label' => '06:00 - 09:00', 'start' => 6, 'end' => 9, 'hint' => 'Apertura temprana'],
            ['label' => '09:00 - 12:00', 'start' => 9, 'end' => 12, 'hint' => 'Bloque de mayor actividad'],
            ['label' => '12:00 - 15:00', 'start' => 12, 'end' => 15, 'hint' => 'Horario de almuerzo y pausas'],
            ['label' => '15:00 - 18:00', 'start' => 15, 'end' => 18, 'hint' => 'Tardes con reuniones continuas'],
            ['label' => '18:00 - 21:00', 'start' => 18, 'end' => 21, 'hint' => 'Sesiones extendidas'],
            ['label' => '21:00 - 24:00', 'start' => 21, 'end' => 24, 'hint' => 'Cierre nocturno'],
        ])->map(function (array $slot) use ($historicalReservations) {
            $count = $historicalReservations->filter(function ($reservation) use ($slot) {
                $hour = $reservation->start_time->hour;

                return $hour >= $slot['start'] && $hour < $slot['end'];
            })->count();

            return [
                'label' => $slot['label'],
                'count' => $count,
                'hint' => $slot['hint'],
            ];
        })->values();

        $peakSlot = $timeSlots->sortByDesc('count')->first();
        $peakCount = max(1, (int) $timeSlots->max('count'));

        $timeSlots = $timeSlots->map(function (array $slot) use ($peakCount) {
            $fill = $peakCount > 0 ? (int) round(($slot['count'] / $peakCount) * 100) : 0;

            return [
                ...$slot,
                'fill' => $fill,
                'level' => $fill >= 80 ? 'Alta' : ($fill >= 50 ? 'Media' : 'Baja'),
            ];
        })->values();

        $stats = [
            'total_spaces'              => Space::count(),
            'active_spaces'             => Space::active()->count(),
            'total_users'               => User::count(),
            'active_users'              => User::whereHas('reservations', fn ($query) => $query->whereIn('status', $confirmedStatuses))->count(),
            'confirmed_count'           => $confirmedReservations->count(),
            'cancelled_count'           => Reservation::where('status', 'cancelled')->count(),
            'today_count'               => Reservation::whereIn('status', $confirmedStatuses)->whereDate('start_time', $today)->count(),
            'this_month_count'          => Reservation::whereIn('status', $confirmedStatuses)
                ->whereBetween('start_time', [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()])
                ->count(),
            'revenue_total'             => (float) $confirmedReservations->sum('total_price'),
            'ratings_count'             => Rating::count(),
            'average_ticket'            => $confirmedReservations->count() > 0 ? round((float) $confirmedReservations->sum('total_price') / $confirmedReservations->count(), 0) : 0,
            'reservations_per_active_user' => User::whereHas('reservations', fn ($query) => $query->whereIn('status', $confirmedStatuses))->count() > 0
                ? round($confirmedReservations->count() / User::whereHas('reservations', fn ($query) => $query->whereIn('status', $confirmedStatuses))->count(), 1)
                : 0,
            'peak_slot_label'           => $peakSlot['label'] ?? 'Sin datos',
            'peak_slot_count'           => $peakSlot['count'] ?? 0,
        ];

        $topSpaces = Space::query()
            ->withCount(['reservations as confirmed_reservations_count' => fn ($query) => $query->whereIn('status', $confirmedStatuses)])
            ->withSum(['reservations as confirmed_revenue' => fn ($query) => $query->whereIn('status', $confirmedStatuses)], 'total_price')
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
                'confirmed_revenue' => (float) ($space->confirmed_revenue ?? 0),
            ]);

        $topUsers = User::query()
            ->whereHas('reservations', fn ($query) => $query->whereIn('status', $confirmedStatuses))
            ->withCount(['reservations as confirmed_reservations_count' => fn ($query) => $query->whereIn('status', $confirmedStatuses)])
            ->withSum(['reservations as confirmed_spend' => fn ($query) => $query->whereIn('status', $confirmedStatuses)], 'total_price')
            ->orderByDesc('confirmed_reservations_count')
            ->limit(6)
            ->get()
            ->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'confirmed_reservations_count' => (int) $user->confirmed_reservations_count,
                'confirmed_spend' => (float) ($user->confirmed_spend ?? 0),
            ]);

        $registeredUsers = User::query()
            ->withCount([
                'reservations',
                'reservations as confirmed_reservations_count' => fn ($query) => $query->whereIn('status', $confirmedStatuses),
            ])
            ->withSum(['reservations as confirmed_spend' => fn ($query) => $query->whereIn('status', $confirmedStatuses)], 'total_price')
            ->withMax('reservations', 'start_time')
            ->latest('created_at')
            ->limit(12)
            ->get()
            ->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_admin' => (bool) $user->is_admin,
                'is_blocked' => (bool) $user->is_blocked,
                'is_suspended' => (bool) $user->is_suspended,
                'email_verified_at' => $user->email_verified_at?->toIso8601String(),
                'created_at' => $user->created_at?->toIso8601String(),
                'reservations_count' => (int) $user->reservations_count,
                'confirmed_reservations_count' => (int) ($user->confirmed_reservations_count ?? 0),
                'confirmed_spend' => (float) ($user->confirmed_spend ?? 0),
                'last_reservation_at' => $user->reservations_max_start_time ? Carbon::parse($user->reservations_max_start_time)->toIso8601String() : null,
                'profile_photo_url' => $user->profile_photo_url,
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
            'stats' => $stats,
            'recentReservations' => $recentReservations,
            'upcomingToday' => $upcomingToday,
            'topSpaces' => $topSpaces,
            'topUsers' => $topUsers,
            'registeredUsers' => $registeredUsers,
            'timeSlots' => $timeSlots,
        ]);
    }
}