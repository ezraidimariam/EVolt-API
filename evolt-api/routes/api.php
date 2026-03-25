<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;

// Simple API Routes - Beginner Level with Sanctum
Route::prefix('api')->group(function () {
    
    // Welcome endpoint
    Route::get('/', function () {
        return response()->json([
            'message' => 'Welcome to Simple EVolt API with Sanctum!',
            'version' => '1.0.0',
            'status' => 'running',
            'description' => 'A simple REST API for learning charging station management with Sanctum authentication',
            'author' => 'Marma',
            'endpoints' => [
                'GET /api/' => 'Welcome message',
                'POST /api/register' => 'Register new user',
                'POST /api/login' => 'Login user',
                'POST /api/logout' => 'Logout user',
                'GET /api/me' => 'Get current user info',
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

    // Public auth routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes with Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        // Auth routes
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

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

});
