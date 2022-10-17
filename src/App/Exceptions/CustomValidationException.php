<?php

namespace App\Exceptions;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CustomValidationException extends CustomException
{
    public function __construct(private readonly Validator $validator)
    {
    }

    public function render(): JsonResponse
    {
        $validation_errors = $this->validator->errors();
        
        return $this->errorResponseFromException(
            $this,
            status_code: Response::HTTP_UNPROCESSABLE_ENTITY,
            description: $validation_errors->toArray()
        );
    }
}
