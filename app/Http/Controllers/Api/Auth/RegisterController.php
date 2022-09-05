<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use \Illuminate\Http\Request;
use App\Traits\ProvidesApiJsonResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        $this->middleware(['ensureStaffEmailProvided', 'guest']);
    }

    /**
     * Undocumented function
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function register(Request $request, UserService $userService)
    {
        $params = $request->validate(
            [
                'name' => 'required|string',
                'email' => 'required|email',
                'password' => 'required|string',
            ]
        );
        try {
            $user =  $userService->createNewUser(
                strtolower($params['email']),
                $params['name'],
                $params['password']
            );

            // dispatch a registered event to send a verification email to the user.
            event(new Registered($user));

            $role_slug = $user->role->slug;
            $token_name = ($request->device_name) ??  $user->name . ' token';

            $token = $user->createToken($token_name, [$role_slug])->plainTextToken;
            return $this->successResponse(
                ['user' => $user, 'token' => $token],
                message: 'user was created successfully',
                status_code: JsonResponse::HTTP_CREATED
            );
        } catch (\Throwable $th) {
            return $this->errorResponseFromException($th);
        }
    }
}
