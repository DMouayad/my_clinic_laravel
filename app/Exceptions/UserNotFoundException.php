<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class UserNotFoundException extends CustomException
{
    public function __construct(private int $user_id)
    {
    }
    /**
     * Render the exception as an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return $this->errorResponseFromException(
            $this,
            'No user found with the id ' . $this->user_id,
            Response::HTTP_NOT_FOUND
        );
    }
}
