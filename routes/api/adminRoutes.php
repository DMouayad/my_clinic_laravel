<?php

use App\Http\Controllers\Api\Admin\StaffEmailController;
use App\Http\Controllers\Api\Admin\UserController;

$admin_middleware = ['middleware' => ['auth:sanctum', 'ability:admin', 'verified']];

/** 
 * User Read-Update-Delete routes
 */
Route::group($admin_middleware, function () {
    Route::apiResource('users', UserController::class)->except('store');
});
