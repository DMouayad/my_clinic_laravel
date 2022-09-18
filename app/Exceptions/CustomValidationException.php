<?php

namespace App\Exceptions;

use App\Traits\ProvidesClassName;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class CustomValidationException extends CustomException
{
    use ProvidesClassName;

    public function __construct(
        private ValidationException $validationException
    ) {
    }

    public function render(): JsonResponse
    {
        $validation_errors = $this->validationException->validator->errors();

        return $this->errorResponseFromException(
            $this,
            status_code: Response::HTTP_UNPROCESSABLE_ENTITY,
            description: $validation_errors->toArray()
        );
    }
}
