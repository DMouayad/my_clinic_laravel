<?php

namespace App\Traits;

use App\Exceptions\CustomValidationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

trait ProvidesCustomRequestValidator
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param array $rules
     * @return array
     * @throws \App\Exceptions\CustomValidationException
     */
    public function customValidate(Request $request, array $rules): array
    {
        try {
            return $request->validate($rules);
        } catch (ValidationException $exception) {
            throw new CustomValidationException($exception);
        }
    }
}
