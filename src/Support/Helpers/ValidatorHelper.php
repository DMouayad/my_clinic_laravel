<?php

namespace Support\Helpers;

use Illuminate\Contracts\Validation\Validator;

class ValidatorHelper
{
    public static function notUnique(Validator $validator, string $field): bool
    {
        return array_key_exists($field, $validator->failed()) &&
            array_key_exists("Unique", $validator->failed()[$field]);
    }

    public static function notInAllowedElements(
        Validator $validator,
        string $field
    ): bool {
        return array_key_exists($field, $validator->failed()) &&
            array_key_exists("In", $validator->failed()[$field]);
    }
}
