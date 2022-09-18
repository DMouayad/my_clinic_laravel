<?php

namespace App\Exceptions;

use App\Traits\ProvidesClassName;
use Symfony\Component\HttpFoundation\Response;

class UnauthorizedToDeleteUserException extends CustomException
{
    use ProvidesClassName;

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
            "Unauthorized to delete this user",
            Response::HTTP_FORBIDDEN
        );
    }
}
