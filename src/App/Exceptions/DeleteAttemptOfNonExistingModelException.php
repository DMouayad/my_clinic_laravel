<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DeleteAttemptOfNonExistingModelException extends CustomException
{
    public function render(): JsonResponse
    {
        return $this->errorResponseFromException(
            $this,
            "Requesting to delete a non existing object",
            Response::HTTP_NOT_FOUND
        );
    }
}
