<?php

namespace App\Exceptions;

use App\Traits\ProvidesExceptionName;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DeleteAttemptOfNonExistingModelException extends CustomException
{
    use ProvidesExceptionName;

    public function render(): JsonResponse
    {
        return $this->errorResponseFromException(
            $this,
            "Requesting to delete non existing object",
            Response::HTTP_NOT_FOUND
        );
    }
}
