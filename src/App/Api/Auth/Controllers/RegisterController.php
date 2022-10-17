<?php

namespace App\Api\Auth\Controllers;

use App\Api\Auth\Requests\RegisterRequest;
use App\Http\Controllers\Controller;
use Domain\Users\Actions\CreateUserAction;
use Domain\Users\DataTransferObjects\CreateUserData;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Support\Traits\ProvidesApiJsonResponse;
use Support\Traits\ProvidesResponseTokens;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    use ProvidesApiJsonResponse, ProvidesResponseTokens;

    /**
     * @param \App\Api\Auth\Requests\RegisterRequest $request
     * @param \Domain\Users\Actions\CreateUserAction $action
     * @return JsonResponse
     */
    public function register(
        RegisterRequest $request,
        CreateUserAction $action
    ): JsonResponse {
        $validated = $request->safe();

        $user = $action->execute(
            new CreateUserData(...$validated->except(["device_id"]))
        );

        // dispatch a registered event to send a verification email to the user.
        event(new Registered($user));

        $role_slug = $user->role->slug;
        $device_id = $validated["device_id"];

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
