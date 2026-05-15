<?php
use App\Http\Controllers\Api\SpaceController as ApiSpaceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Public\SpaceController as PublicSpaceController;
use App\Http\Controllers\Public\ReservationController;
use App\Http\Controllers\Api\ReservationController as ApiReservationController;
use Illuminate\Support\Facades\Route;

// =============================================================
// AUTH — Registro, login, logout
// =============================================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);
    Route::get('/users',   [UserController::class, 'index']);

    Route::post('/spaces', [ApiSpaceController::class, 'store']);
    Route::match(['put', 'patch'], '/spaces/{space}', [ApiSpaceController::class, 'update']);
    Route::delete('/spaces/{space}', [ApiSpaceController::class, 'destroy']);

    Route::get('/reservations', [ApiReservationController::class, 'index']);
    Route::get('/reservations/{reservation}', [ApiReservationController::class, 'show']);
    Route::match(['put', 'patch'], '/reservations/{reservation}', [ApiReservationController::class, 'update']);
    Route::delete('/reservations/{reservation}', [ApiReservationController::class, 'destroy']);
});

// =============================================================
// RUTAS PÚBLICAS — Espacios (sin autenticación)
// =============================================================
Route::get('/spaces', [ApiSpaceController::class, 'index']);
Route::get('/spaces/{space}', [ApiSpaceController::class, 'show']);
Route::get('/spaces/{space}/availability', [ApiSpaceController::class, 'availability']);
// =============================================================
// RESERVAS PÚBLICAS (sin autenticación)
// =============================================================
Route::post('/reservations',             [ApiReservationController::class, 'store']);
Route::get('/reservations/{reservation}/confirmation',
                                         [ApiReservationController::class, 'confirmation']);