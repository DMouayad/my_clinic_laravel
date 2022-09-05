<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use App\Exceptions\CustomException;

class UnauthorizedToDeleteUserException extends CustomException
{
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
            'Email address (' .  $this->email . ') already exists!',
            JsonResponse::HTTP_UNAUTHORIZED
        );
    }
}
