<?php

namespace DDragon\SanctumRefreshToken;

use App\Traits\DateTimeInterface;
use Illuminate\Support\Str;

trait HasRefreshTokens
{
    /**
     * The refresh token the user is using for the current request.
     * @var \DDragon\SanctumRefreshToken\RefreshToken
     */
    protected $refereshToken;

    /**
     * Create a new refresh token for the user.
     *
     * @param string $name
     * @param \DateTimeInterface|null $expiresAt
     * @return \DDragon\SanctumRefreshToken\NewRefreshToken
     */
    public function createRefreshToken(
        string $name,
        DateTimeInterface|null $expiresAt = null
    ) {
        $token = $this->refreshTokens()->create([
            "name" => $name,
            "token" => hash("sha256", $plainTextToken = Str::random(40)),
            "expires_at" => $expiresAt,
        ]);

        return new NewRefreshToken(
            $token,
            $token->getKey() . "|" . $plainTextToken
        );
    }

    /**
     * Get the refresh tokens that belong to model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function refreshTokens()
    {
        return $this->morphMany(RefreshToken::class, "tokenable");
    }

    /**
     * Get the refresh token currently associated with the user.
     *
     *
     */
    public function currentRefreshToken()
    {
        return $this->refereshToken;
    }

    /**
     * Set the current access token for the user.
     *
     * @param RefreshToken $refereshToken
     * @return $this
     */
    public function withRefreshToken($refereshToken)
    {
        $this->refereshToken = $refereshToken;

        return $this;
    }
}
