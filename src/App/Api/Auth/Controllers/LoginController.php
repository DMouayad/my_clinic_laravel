<?php

namespace App\Api\Auth\Controllers;

use App\Api\Auth\Requests\LoginRequest;
use App\Exceptions\InvalidEmailCredentialException;
use App\Exceptions\InvalidPasswordCredentialException;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Support\Traits\ProvidesApiJsonResponse;
use Support\Traits\ProvidesResponseTokens;

class LoginController extends Controller
{
    use ProvidesApiJsonResponse, ProvidesResponseTokens;

    /**
     * Handle a login request to the application.
     *
     * @param \App\Api\Auth\Requests\LoginRequest $request
     * @return JsonResponse
     * @throws \App\Exceptions\InvalidEmailCredentialException
     * @throws \App\Exceptions\InvalidPasswordCredentialException
     */
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        $user = User::where("email", strtolower($validated["email"]))->first();

        if (!$user) {
            throw new InvalidEmailCredentialException();
        }
        if (!Hash::check($validated["password"], $user->password)) {
            throw new InvalidPasswordCredentialException();
        }
        $role_slug = $user->role->slug;
        $device_id = $validated["device_id"];

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
