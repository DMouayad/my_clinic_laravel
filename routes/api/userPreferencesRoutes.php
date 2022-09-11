<?php

namespace Routes\Api;

use App\Http\Controllers\Api\User\UserPreferencesController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('user-preferences', [UserPreferencesController::class, 'show']);
    Route::post('user-preferences', [UserPreferencesController::class, 'store']);
    Route::put('user-preferences', [UserPreferencesController::class, 'update']);
    Route::put('user-preferences', [UserPreferencesController::class, 'update']);
});
