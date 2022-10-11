<?php

namespace App\Exceptions;

use App\Traits\ProvidesExceptionName;
use Illuminate\Http\JsonResponse;

class StaffMemberAlreadyExistsException extends CustomException
{
    use ProvidesExceptionName;

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
            "Email address (" . $this->email . ") already exists!",
            JsonResponse::HTTP_CONFLICT
        );
    }
}
