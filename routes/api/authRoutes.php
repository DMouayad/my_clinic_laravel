<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\VerificationController;
use App\Http\Middleware\EnsureStaffEmailProvided;

Route::post("login", [LoginController::class, "login"])
    ->middleware("guest")
    ->name("api-login");
Route::post("logout", [LogoutController::class, "logout"])->name("api-logout");

Route::middleware(["guest", EnsureStaffEmailProvided::class])
    ->post("register", [RegisterController::class, "register"])
    ->name("api-register");

Route::get("email/verify/{id}/{hash}", [
    VerificationController::class,
    "verify",
])->name("verification.verify");
