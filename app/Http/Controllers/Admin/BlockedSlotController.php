<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedSlot;
use App\Models\Space;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BlockedSlotController extends Controller
{
    public function index()
    {
        $blocks = BlockedSlot::with('space')->orderByDesc('start_time')->get()->map(fn ($b) => [
            'id'         => $b->id,
            'reason'     => $b->reason,
            'start_time' => $b->start_time->toIso8601String(),
            'end_time'   => $b->end_time->toIso8601String(),
            'space'      => ['name' => $b->space->name, 'slug' => $b->space->slug],
        ]);

        return Inertia::render('Admin/BlockedSlots/Index', [
            'blocks'  => $blocks,
            'spaces'  => Space::active()->get(['id', 'name', 'slug']),
            'message' => session('message'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'space_id'   => 'required|exists:spaces,id',
            'start_time' => 'required|date',
            'end_time'   => 'required|date|after:start_time',
            'reason'     => 'nullable|string|max:255',
        ]);

        BlockedSlot::create($request->only(['space_id', 'start_time', 'end_time', 'reason']));

        return redirect()->route('admin.blocked-slots.index')
            ->with('message', 'Bloqueo creado exitosamente.');
    }

    public function destroy(BlockedSlot $blockedSlot)
    {
        $blockedSlot->delete();

        return redirect()->route('admin.blocked-slots.index')
            ->with('message', 'Bloqueo eliminado.');
    }
}