<?php

namespace App\Exceptions;

use App\Traits\ProvidesExceptionName;
use Symfony\Component\HttpFoundation\Response;

class InvalidEmailCredentialException extends CustomException
{
    use ProvidesExceptionName;

    public function render($request)
    {
        return $this->errorResponseFromException(
            $this,
            "Invalid credentials, email address incorrect!",
            Response::HTTP_UNAUTHORIZED,
        );
    }
}
