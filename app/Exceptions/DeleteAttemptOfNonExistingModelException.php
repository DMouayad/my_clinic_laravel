<?php

namespace App\Exceptions;

use App\Traits\ProvidesClassName;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DeleteAttemptOfNonExistingModelException extends CustomException
{
    use ProvidesClassName;

    public function render(): JsonResponse
    {
        return $this->errorResponseFromException(
            $this,
            "Requesting to delete non existing object",
            Response::HTTP_NOT_FOUND
        );
    }
}
