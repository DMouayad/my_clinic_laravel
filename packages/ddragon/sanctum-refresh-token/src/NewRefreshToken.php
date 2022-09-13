<?php

namespace DDragon\SanctumRefreshToken;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class NewRefreshToken implements Arrayable, Jsonable
{
    /**
     * The refresh token instance.
     *
     * @var RefreshToken
     */
    public $refreshToken;

    /**
     * The plain text version of the token.
     *
     * @var string
     */
    public $plainTextToken;

    /**
     * Create a new refresh token result.
     *
     * @param \DDragon\SanctumRefreshToken\RefreshToken $refreshToken
     * @param string $plainTextToken
     * @return void
     */
    public function __construct(
        RefreshToken $refreshToken,
        string       $plainTextToken
    )
    {
        $this->refreshToken = $refreshToken;
        $this->plainTextToken = $plainTextToken;
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            "refreshToken" => $this->refreshToken,
            "plainTextToken" => $this->plainTextToken,
        ];
    }
}
