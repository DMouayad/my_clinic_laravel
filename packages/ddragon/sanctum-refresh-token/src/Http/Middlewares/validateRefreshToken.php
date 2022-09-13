<?php

use DDragon\SanctumRefreshToken\HasRefreshTokens;
use DDragon\SanctumRefreshToken\RefreshToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateRefreshToken
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($token = $this->getTokenFromRequest($request)) {
            $refreshToken = RefreshToken::findToken($token);

            if (
                !$this->isValidRefreshToken($refreshToken) ||
                !$this->supportsTokens($refreshToken->tokenable)
            ) {
                return new JsonResponse(
                    data: [
                        "message" => "Refresh token is expired",
                    ],
                    status: Response::HTTP_UNAUTHORIZED
                );
            }

            return $next($request);
        }
    }

    /**
     * Get the token from the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    protected function getTokenFromRequest(Request $request)
    {
        return $request->bearerToken();
    }

    /**
     * Determine if the provided refresh token is valid.
     *
     * @param mixed $refreshToken
     * @return bool
     */
    protected function isValidRefreshToken($refreshToken): bool
    {
        if (!$refreshToken) {
            return false;
        }
        $expiration = config("sanctumRefreshToken.expiration");
        $isValid =
            (!$expiration ||
                $refreshToken->created_at->gt(
                    now()->subMinutes($expiration)
                )) &&
            (!$refreshToken->expires_at ||
                !$refreshToken->expires_at->isPast());

        return $isValid;
    }

    /**
     * Determine if the tokenable model supports API tokens.
     *
     * @param mixed $tokenable
     * @return bool
     */
    protected function supportsTokens($tokenable = null)
    {
        return $tokenable &&
            in_array(
                HasRefreshTokens::class,
                class_uses_recursive(get_class($tokenable))
            );
    }
}
