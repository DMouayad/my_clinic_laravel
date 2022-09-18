<?php

namespace App\Exceptions;

use App\Traits\ProvidesClassName;
use Symfony\Component\HttpFoundation\Response;

class InvalidEmailCredentialException extends CustomException
{
    use ProvidesClassName;

    public function render($request)
    {
        return $this->errorResponseFromException(
            $this,
            "Invalid credentials, email address incorrect!",
            Response::HTTP_UNAUTHORIZED,
        );
    }
}
