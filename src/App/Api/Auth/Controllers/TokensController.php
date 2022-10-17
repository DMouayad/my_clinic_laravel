<?php

namespace App\Api\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CustomError;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Support\Traits\ProvidesApiJsonResponse;
use Support\Traits\ProvidesResponseTokens;
use Symfony\Component\HttpFoundation\Response;

class TokensController extends Controller
{
    use ProvidesApiJsonResponse, ProvidesResponseTokens;

    public function issueAccessToken(Request $request): JsonResponse
    {
        $device_id = $request->get("device_id");
        $refresh_token = $request->get("refresh_token");
        /**
         * @var \App\Models\User
         */
        $user = $refresh_token->tokenable;
        if (!$user) {
            return $this->errorResponse(
                new CustomError("user not found!"),
                Response::HTTP_UNAUTHORIZED
            );
        }
        // delete previous access tokens which were issued for specified device id
        $user->deleteDeviceTokens($device_id);

        // create new refresh and access tokens
        $tokens = $this->getResponseTokens(
            $user->createRefreshToken($device_id),
            $user->createToken($device_id, [$user->roleSlug()])
        );
        return $this->successResponse($tokens);
    }
}
