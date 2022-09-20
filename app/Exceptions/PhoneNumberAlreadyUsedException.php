<?php

namespace App\Exceptions;

use App\Traits\ProvidesClassName;
use Illuminate\Http\JsonResponse;

class PhoneNumberAlreadyUsedException extends CustomException
{
    use ProvidesClassName;

    public function __construct(private string $phoneNumber)
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
            'Phone number (' . $this->phoneNumber . ') is already used!',
            JsonResponse::HTTP_CONFLICT
        );
    }
}
