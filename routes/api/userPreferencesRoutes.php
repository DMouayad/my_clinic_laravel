<?php

namespace Routes\Api;

use App\Http\Controllers\Api\User\UserPreferencesController;
use Illuminate\Support\Facades\Route;

Route::middleware(["auth:sanctum", "verified"])
    ->group(function () {
        Route::get('me/preferences', [UserPreferencesController::class, 'show']);
        Route::post('me/preferences', [UserPreferencesController::class, 'store']);
        Route::put('me/preferences', [UserPreferencesController::class, 'update']);
        Route::delete('me/preferences', [UserPreferencesController::class, 'destroy']);
    });
