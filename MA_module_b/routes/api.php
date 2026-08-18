<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LineController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\AdminController;

// Public Lines
Route::get('lines', [LineController::class, 'index']);
Route::get('lines/{code}', [LineController::class, 'show']);
Route::get('lines/{code}/timetable', [LineController::class, 'timetable']);

// Public Bookings
Route::post('bookings', [BookingController::class, 'store']);
Route::post('bookings/lookup', [BookingController::class, 'lookup']);
Route::patch('bookings/{code}', [BookingController::class, 'update']);
Route::post('bookings/{code}/cancel', [BookingController::class, 'cancel']);

// Admin Auth
Route::post('admin/login', [AdminController::class, 'login']);

// Protected Admin Routes
Route::middleware('admin.auth')->group(function () {
    
    // Dispatcher & Admin
    Route::get('admin/bookings', [AdminController::class, 'bookings']);
    Route::post('admin/departures/{code}/cancel', [AdminController::class, 'cancelDeparture']);

    // Admin Only
    Route::middleware('admin.role:admin')->group(function () {
        Route::post('admin/lines', [AdminController::class, 'storeLine']);
        Route::put('admin/lines/{code}', [AdminController::class, 'updateLine']);
        Route::post('admin/lines/{code}/service-windows', [AdminController::class, 'storeServiceWindow']);
        Route::delete('admin/lines/{code}/service-windows/{start_time}', [AdminController::class, 'destroyServiceWindow']);
    });
});