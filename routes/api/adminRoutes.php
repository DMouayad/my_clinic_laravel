<?php

use App\Http\Controllers\Api\Admin\StaffMemberController;
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

    Route::get("staff-members", [
        StaffMemberController::class,
        "getStaffMembers",
    ])->name("staff-members");

    Route::get("staff-members-with-roles", [
        StaffMemberController::class,
        "getStaffMembersWithRoles",
    ])->name("staff-members-with-roles");

    Route::get("staff-members-all-data", [
        StaffMemberController::class,
        "getStaffWithUsersAndRoles",
    ])->name("staff-members-full");
});
