<?php

namespace App\Exceptions;

use Support\Traits\ExtractsExceptionName;
use Symfony\Component\HttpFoundation\Response;

class InvalidPasswordCredentialException extends CustomException
{
    use ExtractsExceptionName;

    public function render($request)
    {
        return $this->errorResponseFromException(
            $this,
            "Invalid credentials, incorrect password!",
            Response::HTTP_UNAUTHORIZED
        );
    }
}
