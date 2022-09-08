<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\EnsureStaffEmailProvided;
use App\Services\UserService;
use App\Traits\ProvidesApiJsonResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    use ProvidesApiJsonResponse;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware([EnsureStaffEmailProvided::class, "guest"]);
    }

    /**
     * Undocumented function
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function register(
        Request     $request,
        UserService $userService
    ): JsonResponse
    {
        $params = $request->validate([
            "name" => "required|string",
            "email" => "required|email",
            "password" => "required|string",
        ]);

        $user = $userService->createNewUser(
            strtolower($params["email"]),
            $params["name"],
            $params["password"]
        );

        // dispatch a registered event to send a verification email to the user.
        event(new Registered($user));

        $role_slug = $user->role->slug;
        $token_name = $request->device_name ?? $user->name . " token";

        $token = $user->createToken($token_name, [$role_slug])
            ->plainTextToken;
        return $this->successResponse(
            ["user" => $user, "token" => $token],
            message: "user was created successfully",
            status_code: Response::HTTP_CREATED
        );

    }
}
