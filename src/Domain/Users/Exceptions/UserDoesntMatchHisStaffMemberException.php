<?php

namespace Domain\Users\Exceptions;

use App\Exceptions\CustomException;
use Illuminate\Http\JsonResponse;
use Support\Traits\ExtractsExceptionName;

class UserDoesntMatchHisStaffMemberException extends CustomException
{
    use ExtractsExceptionName;

    public function __construct(private ?string $email)
    {
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request)
    {
        return $this->errorResponseFromException(
            $this,
            "Error updating email/role of the user with email (" .
                $this->email .
                ") due to conflict with the assigned email/role of his staff email",
            JsonResponse::HTTP_CONFLICT
        );
    }
}
