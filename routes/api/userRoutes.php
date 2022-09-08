<?php

namespace Routes\Api;

use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["auth:sanctum", "verified"]], function () {
    Route::get("me", [UserController::class, "show"])->name("get-my-info");
    Route::delete("users/{user}", [UserController::class, "destroy"])
        ->name("delete-my-account");
});
