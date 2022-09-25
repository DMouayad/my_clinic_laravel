<?php

namespace App\Exceptions;

use App\Traits\ProvidesClassName;
use Throwable;

class FailedToSaveObjectException extends CustomException
{
    use ProvidesClassName;

    public function __construct(
        private readonly string $objectClass,
        string $message = "",
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function render()
    {
        return $this->errorResponseFromException(
            $this,
            "Failed to save object of type $this->objectClass!"
        );
    }
}
