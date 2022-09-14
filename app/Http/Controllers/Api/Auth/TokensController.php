<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\CustomError;
use App\Traits\ProvidesApiJsonResponse;
use App\Traits\ProvidesResponseTokens;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokensController extends Controller
{
    use ProvidesApiJsonResponse, ProvidesResponseTokens;

    public function issueAccessToken(Request $request): JsonResponse
    {
        $device_id = $request->get("device_id");
        $refresh_token = $request->get("refresh_token");
        // delete previous access tokens which were issued for specified device id
        $user = $refresh_token->tokenable;
        if (!$user) {
            return $this->errorResponse(
                Response::HTTP_UNAUTHORIZED,
                new CustomError("user not found!")
            );
        }
        $user->deleteDeviceTokens($device_id);

        // create new refresh and access tokens
        $tokens = $this->getResponseTokens(
            $user->createRefreshToken($device_id),
            $user->createToken($device_id, [$user->roleSlug()])
        );
        return $this->successResponse($tokens);
    }
}
