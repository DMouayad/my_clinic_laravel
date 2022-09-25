<?php

namespace App\Exceptions;

use App\Models\User;
use App\Traits\ProvidesExceptionName;
use Illuminate\Http\JsonResponse;

class UserDoesntMatchHisStaffEmailException extends CustomException
{
    use ProvidesExceptionName;

    public function __construct(private User $user)
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
            'Error updating email/role of the user with id (' . $this->user->id . ') due to conflict with the assigned email/role of his staff email',
            JsonResponse::HTTP_CONFLICT
        );
    }
}
