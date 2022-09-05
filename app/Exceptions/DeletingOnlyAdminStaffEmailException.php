<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use App\Exceptions\CustomException;

class DeletingOnlyAdminStaffEmailException extends CustomException
{
    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request)
    {
        return $this->errorResponseFromException(
            $this,
            'Create another staff email with admin role before deleting this only admin email',
            JsonResponse::HTTP_CONFLICT
        );
    }
}
