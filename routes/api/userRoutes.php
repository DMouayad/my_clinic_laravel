<?php

namespace Routes\Api;

use App\Http\Controllers\Api\User\UserController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["auth:sanctum", "verified"]], function () {
    Route::get("me", [UserController::class, "show"])->name("get-my-info");
    Route::delete("me", [UserController::class, "deleteMyAccount"])->name(
        "delete-my-account"
    );
});

Route::post("/email/verification-notification", function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return new JsonResponse("Verification link was sent to your email");
})
    ->middleware(["auth:sanctum", "throttle:6,1"])
    ->name("verification.send");
