<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use App\Exceptions\CustomException;

class EmailAlreadyRegisteredException extends CustomException
{
    public function __construct(private string $email)
    {
    }
    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request)
    {
        return $this->errorResponseFromException(
            $this,
            'Email address (' . $this->email .  ') is already registered',
            JsonResponse::HTTP_CONFLICT
        );
    }
}
