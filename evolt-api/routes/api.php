<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\ChargingStationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;

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

// Simple API Routes - Beginner Level
Route::prefix('api')->group(function () {
    
    // Welcome endpoint
    Route::get('/', function () {
        return response()->json([
            'message' => 'Welcome to Simple EVolt API!',
            'version' => '1.0.0',
            'status' => 'running',
            'description' => 'A simple REST API for learning charging station management',
            'author' => 'Marma',
            'endpoints' => [
                'GET /api/' => 'Welcome message',
                'GET /api/users' => 'List all users',
                'POST /api/users' => 'Create new user',
                'GET /api/users/{id}' => 'Get single user',
                'GET /api/products' => 'List all charging stations',
                'POST /api/products' => 'Create new charging station',
                'GET /api/products/{id}' => 'Get single station',
                'PUT /api/products/{id}' => 'Update station',
                'DELETE /api/products/{id}' => 'Delete station',
                'GET /api/products/search/{name}' => 'Search stations by name'
            ]
        ]);
    });

    // User Routes (Simple)
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
    });

    // Product Routes (Charging Stations)
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::post('/', [ProductController::class, 'store']);
        Route::get('/search/{name}', [ProductController::class, 'search']);
        Route::get('/{id}', [ProductController::class, 'show']);
        Route::put('/{id}', [ProductController::class, 'update']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);
    });

});
