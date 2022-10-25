<?php

namespace Domain\UserPreferences\Exceptions;

use App\Exceptions\CustomException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ProvidedThemePreferenceNotValidException extends CustomException
{
    public function __construct(
        readonly ?string $theme,
        string $message = "",
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function render()
    {
        return $this->errorResponseFromException(
            $this,
            message: "provided theme $this->theme is not valid",
            status_code: Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }
}
