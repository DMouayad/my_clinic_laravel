<?php

namespace App\Exceptions;

use App\Traits\ProvidesClassName;
use Symfony\Component\HttpFoundation\Response;

class UpdateRequestForNonExistingObjectException extends CustomException
{
    use ProvidesClassName;

    public function render($request)
    {
        return $this->errorResponseFromException(
            $this,
            "Requesting to update a non existing object",
            Response::HTTP_NOT_FOUND
        );
    }
}
