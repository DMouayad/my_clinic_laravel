<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class RequestMissingRequiredFields extends CustomException
{
    public function __construct(
        private readonly array $fields,
        string $message = "",
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function render(): JsonResponse
    {
        return $this->errorResponseFromException(
            $this,
            status_code: Response::HTTP_UNPROCESSABLE_ENTITY,
            description: $this->fields
        );
    }
}
