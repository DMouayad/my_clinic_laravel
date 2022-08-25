<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\UserController;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;


Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {

    Route::get('me', [UserController::class, 'me']);
    Route::delete('users/{user}', [UserController::class, 'destroy']);
});
