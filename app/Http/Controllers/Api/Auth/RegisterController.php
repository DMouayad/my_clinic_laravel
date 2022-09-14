<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\EnsureStaffEmailProvided;
use App\Services\UserService;
use App\Traits\ProvidesApiJsonResponse;
use App\Traits\ProvidesResponseTokens;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    use ProvidesApiJsonResponse, ProvidesResponseTokens;

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
     * @param Request $request
     * @param UserService $userService
     * @return JsonResponse
     * @throws \App\Exceptions\EmailAlreadyRegisteredException
     */
    public function register(
        Request $request,
        UserService $userService
    ): JsonResponse {
        $params = $request->validate([
            "name" => "required|string",
            "email" => "required|email",
            "password" => "required|string",
            "device_id" => "required|string",
        ]);

        $user = $userService->createNewUser(
            strtolower($params["email"]),
            $params["name"],
            $params["password"]
        );

        // dispatch a registered event to send a verification email to the user.
        event(new Registered($user));

        $role_slug = $user->role->slug;
        $device_id = $params["device_id"];
        $tokens = $this->getResponseTokens(
            $user->createRefreshToken($device_id),
            $user->createToken($device_id, [$role_slug])
        );
        return $this->successResponse(
            ["user" => $user, ...$tokens],
            message: "user was created successfully",
            status_code: Response::HTTP_CREATED
        );
    }
}
