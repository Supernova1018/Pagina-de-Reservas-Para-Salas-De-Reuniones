<?php

use App\Http\Controllers\Admin\BlockedSlotController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReservationController as AdminReservationController;
use App\Http\Controllers\Admin\SpaceController as AdminSpaceController;
use App\Http\Controllers\Public\ReservationController as PublicReservationController;
use App\Http\Controllers\Public\RatingController as PublicRatingController;
use App\Http\Controllers\Public\SpaceController as PublicSpaceController;
use Illuminate\Support\Facades\Route;

// =============================================================================
// PUBLIC ROUTES (no auth required)
// =============================================================================

// Home — listado de espacios
Route::get('/', [PublicSpaceController::class, 'index'])->name('home');

// Space detail
Route::get('/spaces/{space}', [PublicSpaceController::class, 'show'])->name('public.spaces.show');

// Availability JSON endpoint (AJAX)
Route::get('/spaces/{space}/availability', [PublicSpaceController::class, 'availability'])
    ->name('public.spaces.availability');

// Reservation form
Route::get('/reservations/new', [PublicReservationController::class, 'create'])
    ->name('public.reservations.create');

// Store reservation
Route::post('/reservations', [PublicReservationController::class, 'store'])
    ->name('public.reservations.store');

// Confirmation page
Route::get('/reservations/{reservation}/confirmation', [PublicReservationController::class, 'confirmation'])
    ->name('public.reservations.confirmation');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/my-reservations', [PublicReservationController::class, 'mine'])
        ->name('public.reservations.mine');

    Route::post('/reservations/{reservation}/cancel', [PublicReservationController::class, 'cancel'])
        ->name('public.reservations.cancel');
});

// Rating/feedback for completed reservation
Route::post('/reservations/{reservation}/rating', [PublicRatingController::class, 'store'])
    ->name('public.reservations.rating.store');

// =============================================================================
// ADMIN ROUTES (auth required via Jetstream)
// =============================================================================

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Spaces CRUD (resource)
    Route::resource('spaces', AdminSpaceController::class);

    // Reservations listing + show
    Route::resource('reservations', AdminReservationController::class)->only(['index', 'show']);

    // Reservation state transitions
    Route::post('/reservations/{reservation}/accept', [AdminReservationController::class, 'accept'])
        ->name('reservations.accept');
    Route::post('/reservations/{reservation}/reject', [AdminReservationController::class, 'reject'])
        ->name('reservations.reject');
    Route::post('/reservations/{reservation}/cancel', [AdminReservationController::class, 'cancel'])
        ->name('reservations.cancel');

    // Calendar
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');

    // Blocked slots
    Route::resource('blocked-slots', BlockedSlotController::class)->only(['index', 'store', 'destroy']);
});

// Redirect /home to admin dashboard (Jetstream default)
Route::get('/home', fn () => redirect()->route('admin.dashboard'))
    ->middleware(['auth'])
    ->name('dashboard');