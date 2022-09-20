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
     * @throws \App\Exceptions\CustomValidationException
     */
    public function register(
        Request $request,
        UserService $userService
    ): JsonResponse {
        $params = $this->customValidate($request, [
            "name" => "required|string",
            "email" => "required|email",
            "phone_number" => "required|string",
            "password" => "required|string|min:8",
            "device_id" => "required|string",
        ]);

        $user = $userService->createNewUser(
            email: strtolower($params["email"]),
            name: $params["name"],
            phone_number: $params["phone_number"],
            password: $params["password"]
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
