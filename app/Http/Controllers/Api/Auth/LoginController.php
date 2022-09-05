<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use App\Traits\ProvidesApiJsonResponse;

class LoginController extends Controller
{
    use ProvidesApiJsonResponse;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse 
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::whereEmail(strtolower($credentials['email']))->first();

        if (!$user) {
            return $this->errorResponse(
                ['message' => 'invalid credentials - email not found!'],
                JsonResponse::HTTP_UNAUTHORIZED
            );
        }
        if (!Hash::check($request->password, $user->password)) {
            return
                $this->errorResponse(
                    ['message' => 'invalid credentials - incorrect password!'],
                    JsonResponse::HTTP_UNAUTHORIZED
                );
        }
        $role_slug = $user->role->slug;
        $token_name = ($request->device_name) ??  $user->name . ' token';

        $token = $user->createToken($token_name, [$role_slug])->plainTextToken;

        return $this->successResponse(
            [
                'id' => $user->id,
                'token' => $token,
                'role' => $role_slug
            ]
        );
    }
}
