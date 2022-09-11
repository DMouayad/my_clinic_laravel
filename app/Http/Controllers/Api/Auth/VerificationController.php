<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\CustomError;
use App\Models\User;
use App\Traits\ProvidesApiJsonResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use ProvidesApiJsonResponse;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("auth:sanctum")->only("resend");
        $this->middleware("signed")->only("verify");
        $this->middleware("throttle:6,1")->only("verify", "resend");
    }

    /**
     * Mark the authenticated user’s email address as verified.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request)
    {
        $user = User::find($request->route("id"));

        if (
            !hash_equals(
                (string) $request->route("id"),
                (string) $user->getKey()
            )
        ) {
            throw new AuthorizationException();
        }

        if (
            !hash_equals(
                (string) $request->route("hash"),
                sha1($user->getEmailForVerification())
            )
        ) {
            throw new AuthorizationException();
        }

        if ($user->hasVerifiedEmail()) {
            return $this->errorResponse(
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                new CustomError("user email already verified")
            );
        }
        $user->markEmailAsVerified();
        return $this->successResponse(
            message: "User email was verified successfully"
        );
    }

    /**
     * Resend the email verification notification.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->successResponse(
                status_code: JsonResponse::HTTP_NO_CONTENT
            );
        }
        $request->user()->sendEmailVerificationNotification();
        return $this->successResponse(
            message: "A Verification email will be sent to your email, please check your inbox."
        );
    }
}
