<?php

namespace App\Exceptions;

use App\Traits\ProvidesExceptionName;
use Symfony\Component\HttpFoundation\Response;

class InvalidPasswordCredentialException extends CustomException
{
    use ProvidesExceptionName;

    public function render($request)
    {
        return $this->errorResponseFromException(
            $this,
            "Invalid credentials, incorrect password!",
            Response::HTTP_UNAUTHORIZED,
        );
    }
}
