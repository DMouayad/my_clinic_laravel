<?php

namespace App\Exceptions;

use App\Traits\ProvidesExceptionName;
use Illuminate\Http\JsonResponse;

class DeletingOnlyAdminStaffEmailException extends CustomException
{
    use ProvidesExceptionName;

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
            'Cannot delete the only admin staff email, Create another staff email with admin role before deleting this one',
            JsonResponse::HTTP_CONFLICT
        );
    }
}
