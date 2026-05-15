<?php

namespace App\Services;

use App\Models\Space;
use App\Models\Reservation;
use App\Models\BlockedSlot;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AvailabilityService
{
    private int $slotMinutes;

    public function __construct()
    {
        $this->slotMinutes = (int) env('RESERVATION_SLOT_MINUTES', 60);
    }

    /**
     * Check if a specific time slot is available for a space.
     */
    public function isSlotAvailable(Space $space, Carbon $start, Carbon $end, ?int $excludeReservationId = null): bool
    {
        // 1. Check within weekly availability
        if (!$this->isWithinAvailability($space, $start, $end)) {
            return false;
        }

        // 2. Check for blocked slots
        if ($this->isBlocked($space, $start, $end)) {
            return false;
        }

        // 3. Check for overlapping reservations
        if ($this->hasOverlappingReservation($space, $start, $end, $excludeReservationId)) {
            return false;
        }

        return true;
    }

    /**
     * Returns available slots for a space within a date range.
     */
    public function getAvailableSlots(Space $space, Carbon $from, Carbon $to): array
    {
        $slots = [];
        $current = $from->copy()->startOfHour();

        while ($current->lt($to)) {
            $slotEnd = $current->copy()->addMinutes($this->slotMinutes);

            if ($slotEnd->gt($to)) {
                break;
            }

            if ($this->isSlotAvailable($space, $current->copy(), $slotEnd->copy())) {
                $slots[] = [
                    'start' => $current->toIso8601String(),
                    'end'   => $slotEnd->toIso8601String(),
                    'label' => $current->format('H:i') . ' - ' . $slotEnd->format('H:i'),
                    'date'  => $current->format('Y-m-d'),
                ];
            }

            $current->addMinutes($this->slotMinutes);
        }

        return $slots;
    }

    /**
     * Get the next N available slots for a space starting from now.
     */
    public function getNextAvailableSlots(Space $space, int $count = 10): array
    {
        $slots  = [];
        $from   = Carbon::now()->addHour()->startOfHour();
        $to     = $from->copy()->addDays(30);
        $all    = $this->getAvailableSlots($space, $from, $to);

        return array_slice($all, 0, $count);
    }

    /**
     * Get a full week calendar view for a space.
     * Returns days with slots that show reservations, blocks and free slots.
     */
    public function getWeekCalendar(Space $space, Carbon $weekStart): array
    {
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        // Pre-load data for the week
        $reservations = Reservation::where('space_id', $space->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('start_time', '>=', $weekStart)
            ->where('end_time', '<=', $weekEnd->copy()->endOfDay())
            ->get();

        $blockedSlots = BlockedSlot::where('space_id', $space->id)
            ->where('start_time', '>=', $weekStart)
            ->where('end_time', '<=', $weekEnd->copy()->endOfDay())
            ->get();

        $calendar = [];
        $period = CarbonPeriod::create($weekStart, '1 day', $weekEnd);

        foreach ($period as $day) {
            $dayOfWeek   = $day->dayOfWeek; // 0=Sun ... 6=Sat
            $availability = $space->availabilities->where('day_of_week', $dayOfWeek)->first();

            $daySlots = [];

            if ($availability) {
                $dayStart = $day->copy()->setTimeFromTimeString($availability->start_time);
                $dayEnd   = $day->copy()->setTimeFromTimeString($availability->end_time);
                $current  = $dayStart->copy();

                while ($current->lt($dayEnd)) {
                    $slotEnd = $current->copy()->addMinutes($this->slotMinutes);

                    if ($slotEnd->gt($dayEnd)) {
                        break;
                    }

                    // Determine slot type
                    $reservation = $reservations->first(function ($r) use ($current, $slotEnd) {
                        return $r->start_time->lt($slotEnd) && $r->end_time->gt($current);
                    });

                    $block = $blockedSlots->first(function ($b) use ($current, $slotEnd) {
                        return $b->start_time->lt($slotEnd) && $b->end_time->gt($current);
                    });

                    $type = 'available';
                    $meta = null;

                    if ($block) {
                        $type = 'blocked';
                        $meta = ['reason' => $block->reason, 'id' => $block->id];
                    } elseif ($reservation) {
                        $type = $reservation->status; // 'pending' | 'confirmed'
                        $meta = [
                            'id'        => $reservation->id,
                            'slug'      => $reservation->slug,
                            'user_name' => $reservation->user_name,
                            'status'    => $reservation->status,
                        ];
                    }

                    $daySlots[] = [
                        'start' => $current->toIso8601String(),
                        'end'   => $slotEnd->toIso8601String(),
                        'label' => $current->format('H:i'),
                        'type'  => $type,
                        'meta'  => $meta,
                    ];

                    $current->addMinutes($this->slotMinutes);
                }
            }

            $calendar[] = [
                'date'      => $day->format('Y-m-d'),
                'dayName'   => $day->isoFormat('dddd'),
                'dayNumber' => $day->day,
                'slots'     => $daySlots,
                'available' => !empty($availability),
            ];
        }

        return $calendar;
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function isWithinAvailability(Space $space, Carbon $start, Carbon $end): bool
    {
        $dayOfWeek = $start->dayOfWeek;

        $availability = $space->availabilities
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$availability) {
            return false;
        }

        $availStart = Carbon::createFromTimeString($availability->start_time);
        $availEnd   = Carbon::createFromTimeString($availability->end_time);

        $slotStart = Carbon::createFromTimeString($start->format('H:i:s'));
        $slotEnd   = Carbon::createFromTimeString($end->format('H:i:s'));

        return $slotStart->gte($availStart) && $slotEnd->lte($availEnd);
    }

    private function isBlocked(Space $space, Carbon $start, Carbon $end): bool
    {
        return BlockedSlot::where('space_id', $space->id)
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->exists();
    }

    private function hasOverlappingReservation(Space $space, Carbon $start, Carbon $end, ?int $excludeId = null): bool
    {
        return Reservation::where('space_id', $space->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists();
    }
}
