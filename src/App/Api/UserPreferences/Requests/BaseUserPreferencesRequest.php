<?php

namespace App\Api\UserPreferences\Requests;

use App\Exceptions\CustomValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

abstract class BaseUserPreferencesRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function rules()
    {
        return [
            "theme" => $this->getThemeRules(),
            "locale" => $this->getLocaleRules(),
        ];
    }

    abstract public function getThemeRules(): array|string;

    abstract public function getLocaleRules(): array|string;

    protected function failedValidation(Validator $validator)
    {
        throw new CustomValidationException($validator);
    }
}
