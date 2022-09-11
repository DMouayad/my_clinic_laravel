<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\CustomException;

class UserPreferencesAlreadyExistsException extends CustomException
{
    public function __construct(private int $user_id)
    {
    }
    /**
     * Render the exception as an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request)
    {
        return $this->errorResponseFromException(
            $this,
            'Preferences for the user with id (' . $this->user_id . ') were already saved!',
            Response::HTTP_CONFLICT
        );
    }
}
