<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Users/Index', [
            'users' => $this->userRows(),
            'message' => session('message'),
        ]);
    }

    public function edit(User $user)
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => $this->formatUser($user),
            'message' => session('message'),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_admin' => ['sometimes', 'boolean'],
            'is_blocked' => ['sometimes', 'boolean'],
            'is_suspended' => ['sometimes', 'boolean'],
            'email_verified' => ['sometimes', 'boolean'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->is_admin = $request->boolean('is_admin');
        $user->is_blocked = $request->boolean('is_blocked');
        $user->is_suspended = $request->boolean('is_suspended');
        $user->email_verified_at = $request->boolean('email_verified') ? ($user->email_verified_at ?? now()) : null;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users.edit', $user)
            ->with('message', 'Usuario actualizado correctamente.');
    }

    public function block(User $user)
    {
        $user->update([
            'is_blocked' => true,
            'is_suspended' => false,
        ]);

        return back()->with('message', 'Usuario bloqueado correctamente.');
    }

    public function unblock(User $user)
    {
        $user->update(['is_blocked' => false]);

        return back()->with('message', 'Usuario desbloqueado correctamente.');
    }

    public function suspend(User $user)
    {
        $user->update(['is_suspended' => true]);

        return back()->with('message', 'Usuario suspendido correctamente.');
    }

    public function unsuspend(User $user)
    {
        $user->update(['is_suspended' => false]);

        return back()->with('message', 'Usuario reactivado correctamente.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('message', 'Usuario eliminado correctamente.');
    }

    private function userRows()
    {
        return User::query()
            ->withCount([
                'reservations',
                'reservations as confirmed_reservations_count' => fn ($query) => $query->whereIn('status', ['confirmed', 'completed']),
            ])
            ->withSum(['reservations as confirmed_spend' => fn ($query) => $query->whereIn('status', ['confirmed', 'completed'])], 'total_price')
            ->withMax('reservations', 'start_time')
            ->latest('created_at')
            ->get()
            ->map(fn ($user) => $this->formatUser($user));
    }

    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'is_admin' => (bool) $user->is_admin,
            'is_blocked' => (bool) $user->is_blocked,
            'is_suspended' => (bool) $user->is_suspended,
            'email_verified_at' => $user->email_verified_at?->toIso8601String(),
            'created_at' => $user->created_at?->toIso8601String(),
            'reservations_count' => (int) ($user->reservations_count ?? 0),
            'confirmed_reservations_count' => (int) ($user->confirmed_reservations_count ?? 0),
            'confirmed_spend' => (float) ($user->confirmed_spend ?? 0),
            'last_reservation_at' => $user->reservations_max_start_time ? Carbon::parse($user->reservations_max_start_time)->toIso8601String() : null,
            'profile_photo_url' => $user->profile_photo_url,
        ];
    }
}