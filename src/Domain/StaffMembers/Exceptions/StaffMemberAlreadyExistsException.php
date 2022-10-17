<?php

namespace Domain\StaffMembers\Exceptions;

use App\Exceptions\CustomException;
use Symfony\Component\HttpFoundation\Response;

class StaffMemberAlreadyExistsException extends CustomException
{
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
            "A staff member already exists with this email!",
            Response::HTTP_CONFLICT
        );
    }
}
