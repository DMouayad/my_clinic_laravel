<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class EmailAlreadyRegisteredException extends CustomException
{
    public function __construct(private string $email)
    {
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request)
    {
        return $this->errorResponseFromException(
            $this,
            "Email address (" . $this->email . ") is already registered",
            Response::HTTP_CONFLICT
        );
    }
}
