<?php

use App\Api\Admin\StaffMembers\Controllers\StaffMemberController;
use App\Api\Users\Controllers\UserController;

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
 * StaffMember CRUD routes
 */
Route::group($admin_middleware, function () {
    Route::apiResource("staff-members", StaffMemberController::class)
        ->except("index")
        ->names([
            "store" => "store-staff-member",
            "update" => "update-staff-member",
            "destroy" => "delete-staff-member",
        ]);

    Route::controller(StaffMemberController::class)->group(function () {
        Route::get("staff-members", "index")->name("staff-members");

        Route::get("staff-members-with-roles", "indexWithRoles")->name(
            "staff-members-with-roles"
        );

        Route::get("staff-members-all-data", "indexWithRolesAndUsers")->name(
            "staff-members-full"
        );
    });
});
