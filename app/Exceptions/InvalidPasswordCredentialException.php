<?php

namespace App\Exceptions;

use App\Traits\ProvidesClassName;
use Symfony\Component\HttpFoundation\Response;

class InvalidPasswordCredentialException extends CustomException
{
    use ProvidesClassName;

    public function render($request)
    {
        return $this->errorResponseFromException(
            $this,
            "Invalid credentials, incorrect password!",
            Response::HTTP_UNAUTHORIZED,
        );
    }
}
