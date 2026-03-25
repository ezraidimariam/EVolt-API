<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\ChargingStationController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Reservations
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::get('/mes-reservations', [ReservationController::class, 'myReservations']);
    Route::post('/reservations/{id}/pay', [ReservationController::class, 'pay']);
    Route::post('/reservations/{id}/cancel', [ReservationController::class, 'cancel']);

    // Charging stations
    Route::get('/charging-stations', [ChargingStationController::class, 'index']);
    Route::get('/charging-stations/search', [ChargingStationController::class, 'search']);

    // Admin routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [ReservationController::class, 'dashboard']);
        Route::post('/charging-stations', [ChargingStationController::class, 'store']);
        Route::put('/charging-stations/{id}', [ChargingStationController::class, 'update']);
        Route::delete('/charging-stations/{id}', [ChargingStationController::class, 'destroy']);
    });
});
