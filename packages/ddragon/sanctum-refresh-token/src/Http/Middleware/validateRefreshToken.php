<?php

namespace DDragon\SanctumRefreshToken\Http\Middleware;

use Closure;
use DDragon\SanctumRefreshToken\HasRefreshTokens;
use DDragon\SanctumRefreshToken\ProvidesCustomRequestValidator;
use DDragon\SanctumRefreshToken\RefreshToken;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateRefreshToken
{
    use ProvidesCustomRequestValidator;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $params = $this->customValidate($request, [
            "device_id" => "required|string",
        ]);
        $device_id = $params["device_id"];
        if ($token = $this->getTokenFromRequest($request)) {
            $refreshToken = RefreshToken::findToken($token);
            if (
                !$refreshToken ||
                !$this->verifyDeviceId($refreshToken, $device_id) ||
                !$this->supportsTokens($refreshToken->tokenable)
            ) {
                return new JsonResponse(
                    ["message" => "invalid token"],
                    status: Response::HTTP_UNAUTHORIZED
                );
            }
            if ($this->isTokenExpired($refreshToken)) {
                return new JsonResponse(
                    ["message" => "Refresh token expired!"],
                    status: Response::HTTP_UNAUTHORIZED
                );
            }
            // add refresh token to request body
            $request->request->add(["refresh_token" => $refreshToken]);
            return $next($request);
        }
        throw new AuthenticationException();
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

    protected function verifyDeviceId(
        RefreshToken $refreshToken,
        string $device_id
    ): bool {
        return $refreshToken->name == $device_id;
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

    /**
     * Determine if the provided refresh token is valid.
     *
     * @param RefreshToken $refreshToken
     * @return bool
     */
    protected function isTokenExpired($refreshToken): bool
    {
        $expiration = config("sanctumRefreshToken.expiration");

        $isValid =
            (!$expiration ||
                $refreshToken->created_at->gt(
                    now()->subMinutes($expiration)
                )) &&
            (!$refreshToken->expires_at ||
                !$refreshToken->expires_at->isPast());

        return !$isValid;
    }
}
