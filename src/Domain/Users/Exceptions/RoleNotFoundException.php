<?php

namespace Domain\Users\Exceptions;

use App\Exceptions\CustomException;
use Illuminate\Http\JsonResponse;

class RoleNotFoundException extends CustomException
{
    public function __construct(private string $role_slug)
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
            "The role (" .
                $this->role_slug .
                ") was not found, Please provide a valid role slug.",
            JsonResponse::HTTP_UNPROCESSABLE_ENTITY
        );
    }
}
