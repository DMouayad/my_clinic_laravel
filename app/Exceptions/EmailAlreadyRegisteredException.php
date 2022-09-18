<?php

namespace App\Exceptions;

use App\Traits\ProvidesClassName;
use Illuminate\Http\JsonResponse;

class EmailAlreadyRegisteredException extends CustomException
{
    use ProvidesClassName;

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
            'Email address (' . $this->email . ') is already registered',
            JsonResponse::HTTP_CONFLICT
        );
    }
}
