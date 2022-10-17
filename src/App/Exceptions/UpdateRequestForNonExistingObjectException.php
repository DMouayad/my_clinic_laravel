<?php

namespace App\Exceptions;

use Support\Traits\ExtractsExceptionName;
use Symfony\Component\HttpFoundation\Response;

class UpdateRequestForNonExistingObjectException extends CustomException
{
    use ExtractsExceptionName;

    public function render($request)
    {
        return $this->errorResponseFromException(
            $this,
            "Requesting to update a non existing object",
            Response::HTTP_NOT_FOUND
        );
    }
}
