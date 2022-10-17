<?php

namespace Routes\Api;

use App\Api\UserPreferences\Controllers\UserPreferencesController;
use Illuminate\Support\Facades\Route;

Route::middleware(["auth:sanctum", "verified"])
    ->name("user.preferences.")
    ->controller(UserPreferencesController::class)
    ->group(function () {
        Route::get("me/preferences", "show")->name("get");
        Route::post("me/preferences", "store")->name("add");
        Route::put("me/preferences", "update")->name("update");
        Route::delete("me/preferences", "destroy")->name("delete");
    });
