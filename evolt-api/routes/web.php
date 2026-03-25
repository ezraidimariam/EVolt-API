<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\StationController;
use App\Http\Controllers\Web\ReservationController;
use App\Http\Controllers\Web\AdminController;

// Public routes
Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/login', [HomeController::class, 'login'])->name('login');
Route::get('/register', [HomeController::class, 'register'])->name('register');

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::get('/stations', [StationController::class, 'index'])->name('stations');
    Route::get('/reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
    
    // Admin routes
    Route::middleware(['admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/stations', [AdminController::class, 'stations'])->name('admin.stations');
        Route::get('/stations/create', [AdminController::class, 'createStation'])->name('admin.stations.create');
        Route::post('/stations', [AdminController::class, 'storeStation'])->name('admin.stations.store');
        Route::get('/stations/{id}/edit', [AdminController::class, 'editStation'])->name('admin.stations.edit');
        Route::put('/stations/{id}', [AdminController::class, 'updateStation'])->name('admin.stations.update');
        Route::delete('/stations/{id}', [AdminController::class, 'deleteStation'])->name('admin.stations.delete');
    });
});

// Logout
Route::post('/logout', function () {
    auth()->logout();
    return redirect('/login');
})->name('logout');
