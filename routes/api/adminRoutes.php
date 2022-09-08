<?php

use App\Http\Controllers\Api\Admin\StaffEmailController;
use App\Http\Controllers\Api\User\UserController;

$admin_middleware = [
    "middleware" => ["auth:sanctum", "ability:admin", "verified"],
];

/**
 * User Read-Update-Delete routes
 */
Route::group($admin_middleware, function () {
    Route::apiResource("users", UserController::class)
        ->except(["store", "update"])
        ->names([
            "index" => "get-all-users",
            "destroy" => "delete-user",
        ]);
    Route::get("staff-users", [UserController::class, "getStaffUsers"])->name(
        "get-staff-users"
    );
});

/**
 * StaffEmail CRUD routes
 */
Route::group($admin_middleware, function () {
    Route::apiResource("staff-emails", StaffEmailController::class)
        ->except("index")
        ->names([
            "store" => "store-staff-email",
            "update" => "update-staff-email",
            "destroy" => "delete-staff-email",
        ]);

    Route::get("staff-emails", [
        StaffEmailController::class,
        "getEmailsOnly",
    ])->name("get-staff-emails");

    Route::get("staff-emails-with-roles", [
        StaffEmailController::class,
        "getEmailsWithRoles",
    ])->name("get-staff-emails-with-roles");
});
