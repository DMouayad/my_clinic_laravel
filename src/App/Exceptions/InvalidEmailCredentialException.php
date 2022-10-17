<?php

namespace App\Exceptions;

use Support\Traits\ExtractsExceptionName;
use Symfony\Component\HttpFoundation\Response;

class InvalidEmailCredentialException extends CustomException
{
    use ExtractsExceptionName;

    public function render($request)
    {
        return $this->errorResponseFromException(
            $this,
            "Invalid credentials, email address incorrect!",
            Response::HTTP_UNAUTHORIZED
        );
    }
}
