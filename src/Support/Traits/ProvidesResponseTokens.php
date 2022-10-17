<?php

namespace Support\Traits;

use Carbon\Carbon;
use DDragon\SanctumRefreshToken\NewRefreshToken;
use Laravel\Sanctum\NewAccessToken;

trait ProvidesResponseTokens
{
    /**
     * @param NewRefreshToken $refreshToken
     * @param NewAccessToken $accessToken
     * @return array
     */
    public function getResponseTokens(
        NewRefreshToken $refreshToken,
        NewAccessToken  $accessToken
    ): array
    {
        // get access token expires_at if it was specified for this token
        // or else get default expiration for sanctum PersonalAccessToken
        $accessTokenExpiresAt =
            $accessToken->accessToken->expires_at ??
            config("sanctum.expiration")
                ? Carbon::now()->addMinutes(config("sanctum.expiration"))
                : null;

        return [
            "access_token" => [
                "token" => $accessToken->plainTextToken,
                "expires_at" => $accessTokenExpiresAt,
            ],
            "refresh_token" => $refreshToken->plainTextToken,
        ];
    }
}
