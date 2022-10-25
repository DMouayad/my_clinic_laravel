<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class InvalidEmailCredentialException extends CustomException
{

    public function render($request)
    {
        return $this->errorResponseFromException(
            $this,
            "Invalid credentials, email address incorrect!",
            Response::HTTP_UNAUTHORIZED
        );
    }
}
