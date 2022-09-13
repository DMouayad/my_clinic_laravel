<?php

namespace Routes\Api;

use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


Route::group(["middleware" => ["auth:sanctum", "verified"]], function () {
    Route::get("me", [UserController::class, "show"])->name("get-my-info");
    Route::delete("users/{user}", [UserController::class, "destroy"])
        ->name("delete-my-account");
});

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return new JsonResponse('Verification link was sent to your email');
})->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');
