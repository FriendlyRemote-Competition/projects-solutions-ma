<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LineController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\AdminController;

Route::prefix('MA_Module_B/api')->group(function () {
    Route::get('lines', [LineController::class, 'index']);
    Route::get('lines/{code}', [LineController::class, 'show']);
    Route::get('lines/{code}/timetable', [LineController::class, 'timetable']);

    Route::post('bookings', [BookingController::class, 'store']);
    Route::post('bookings/lookup', [BookingController::class, 'lookup']);
    Route::patch('bookings/{code}', [BookingController::class, 'update']);
    Route::post('bookings/{code}/cancel', [BookingController::class, 'cancel']);

    Route::post('admin/login', [AdminController::class, 'login']);

    Route::middleware('admin.auth')->group(function () {
        Route::get('admin/bookings', [AdminController::class, 'bookings']);
        Route::post('admin/departures/{code}/cancel', [AdminController::class, 'cancelDeparture']);

        Route::middleware('admin.role:admin')->group(function () {
            Route::post('admin/lines', [AdminController::class, 'storeLine']);
            Route::put('admin/lines/{code}', [AdminController::class, 'updateLine']);
            Route::post('admin/lines/{code}/service-windows', [AdminController::class, 'storeServiceWindow']);
            Route::delete('admin/lines/{code}/service-windows/{start_time}', [AdminController::class, 'destroyServiceWindow']);
        });
    });
});