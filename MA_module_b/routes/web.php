<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\BoardController;
use App\Http\Controllers\Web\StatsController;

Route::prefix('MA_Module_B')->group(function () {
    Route::get('board', [BoardController::class, 'index']);
    Route::get('board/{station}', [BoardController::class, 'show']);
    Route::get('stats', [StatsController::class, 'index']);
});