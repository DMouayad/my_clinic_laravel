<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\TokensController;
use App\Http\Controllers\Api\Auth\VerificationController;
use App\Http\Middleware\EnsureStaffEmailProvided;
use DDragon\SanctumRefreshToken\Http\Middleware\ValidateRefreshToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// login route
Route::post("login", [LoginController::class, "login"])
    ->middleware("guest")
    ->name("api-login");

// logout route
Route::post("logout", [LogoutController::class, "logout"])
    ->middleware("auth:sanctum")
    ->name("api-logout");

// register route
Route::post("register", [RegisterController::class, "register"])
    ->middleware(["guest", EnsureStaffEmailProvided::class])
    ->name("api-register");

// verify email route
Route::get("email/verify/{id}/{hash}", [
    VerificationController::class,
    "verify",
])->name("verification.verify");

// send email verification
Route::post("/email/verification-notification", function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return new JsonResponse("Verification link was sent to your email");
})
    ->middleware(["auth:sanctum", "throttle:6,1"])
    ->name("verification.send");

// request access token route
Route::post("/token", [TokensController::class, "issueAccessToken"])
    ->middleware([ValidateRefreshToken::class, "guest"])
    ->name("api-access-token");
