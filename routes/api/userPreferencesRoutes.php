<?php

namespace Routes\Api;

use App\Http\Controllers\Api\User\UserPreferencesController;
use Illuminate\Support\Facades\Route;

Route::middleware(["auth:sanctum", "verified"])
    ->name("user.preferences.")
    ->group(function () {
        Route::get("me/preferences", [
            UserPreferencesController::class,
            "show",
        ])->name("get");
        //
        Route::post("me/preferences", [
            UserPreferencesController::class,
            "store",
        ])->name("add");
        //
        Route::put("me/preferences", [
            UserPreferencesController::class,
            "update",
        ])->name("update");
        //
        Route::delete("me/preferences", [
            UserPreferencesController::class,
            "destroy",
        ])->name("delete");
    });
