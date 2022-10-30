<?php

    namespace Routes\Api;

    use App\Api\Users\Controllers\UserController;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;

    Route::middleware(["auth:sanctum"])
        ->controller(UserController::class)
        ->group(function (){
            Route::middleware("verified")->group(function (){
                Route::put("me", "update")->name("update-my-info");
                Route::delete("me", "deleteCurrentUser")->name("delete-my-account");
            });
            Route::get("me", "show")->name("get-my-info");
        });

    Route::post("/email/verification-notification",
        function (Request $request){
            $request->user()->sendEmailVerificationNotification();

            return new JsonResponse("Verification link was sent to your email");
        })
        ->middleware(["auth:sanctum", "throttle:6,1"])
        ->name("verification.send");
