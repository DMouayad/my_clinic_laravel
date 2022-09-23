<?php

namespace App\Traits;

use App\Exceptions\CustomValidationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

trait ProvidesCustomRequestValidator
{
    /** Validates that request includes all specified rules
     *
     *  uses http request validator but Throws a customValidationException
     * in case of an invalid or a missing required parameter.
     *
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
