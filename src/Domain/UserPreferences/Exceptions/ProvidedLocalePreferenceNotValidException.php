<?php

namespace Domain\UserPreferences\Exceptions;

use App\Exceptions\CustomException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ProvidedLocalePreferenceNotValidException extends CustomException
{
    public function __construct(
        readonly ?string $locale,
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
            message: "provided locale preference $this->locale is not valid",
            status_code: Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }
}
