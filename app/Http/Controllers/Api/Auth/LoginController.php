<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\CustomError;
use App\Models\User;
use App\Traits\ProvidesApiJsonResponse;
use App\Traits\ProvidesResponseTokens;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    use ProvidesApiJsonResponse, ProvidesResponseTokens;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("guest");
    }

    /**
     * Handle a login request to the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $params = $request->validate([
            "email" => "required|email",
            "password" => "required|string|min:8",
            "device_id" => "required|string",
        ]);

        $user = User::whereEmail(strtolower($params["email"]))->first();

        if (!$user) {
            return $this->errorResponse(
                Response::HTTP_UNAUTHORIZED,
                new CustomError("invalid credentials - email not found!")
            );
        }
        if (!Hash::check($params["password"], $user->password)) {
            return $this->errorResponse(
                Response::HTTP_UNAUTHORIZED,
                new CustomError("invalid credentials - incorrect password!")
            );
        }
        $role_slug = $user->role->slug;
        $device_id = $params["device_id"];
        if (config("my_clinic.delete_device_previous_auth_tokens_on_login")) {
            $user->deleteDeviceTokens($device_id);
        }
        $tokens_arr = $this->getResponseTokens(
            $user->createRefreshToken($device_id),
            $user->createToken($device_id, [$role_slug])
        );

        return $this->successResponse([
            "user" => $user->load("preferences"),
            ...$tokens_arr,
        ]);
    }


}
