<?php

namespace Routes\Api;

use App\Http\Controllers\Api\User\UserPreferencesController;
use Illuminate\Support\Facades\Route;

Route::middleware(["auth:sanctum", "verified"])->group(function () {
    Route::apiResource(
        "userPreferences",
        UserPreferencesController::class
    )->except(["index"]);
});
