<?php

namespace App\Exceptions;

use App\Traits\ProvidesExceptionName;
use Symfony\Component\HttpFoundation\Response;

class EmailUnauthorizedToRegisterException extends CustomException
{
    use ProvidesExceptionName;

    /**
     * Render the exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __construct(private string $email)
    {
    }

    public function render($request)
    {
        return $this->errorResponseFromException(
            $this,
            "The email address (" . $this->email . ") is not allowed to register.",
            Response::HTTP_FORBIDDEN,
        );
    }
}
