<?php

namespace App\Exceptions;

use App\Traits\ProvidesExceptionName;
use Symfony\Component\HttpFoundation\Response;

class UpdateRequestForNonExistingObjectException extends CustomException
{
    use ProvidesExceptionName;

    public function render($request)
    {
        return $this->errorResponseFromException(
            $this,
            "Requesting to update a non existing object",
            Response::HTTP_NOT_FOUND
        );
    }
}
