<?php

namespace App\Exceptions;

use Support\Traits\ExtractsExceptionName;
use Symfony\Component\HttpFoundation\Response;

class UserPreferencesAlreadyExistsException extends CustomException
{
    use ExtractsExceptionName;

    public function __construct(private int $user_id)
    {
    }

    /**
     * Render the exception as an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request)
    {
        return $this->errorResponseFromException(
            $this,
            "Preferences for the user with id (" .
                $this->user_id .
                ") already saved!",
            Response::HTTP_CONFLICT
        );
    }
}
