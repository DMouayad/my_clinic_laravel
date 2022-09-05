<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\VerificationController;

Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LogoutController::class, 'logout']);

Route::middleware('ensureStaffEmailProvided')->post(
    'register',
    [RegisterController::class, 'register']
);

Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
