<?php

namespace App\Api\UserPreferences\Requests;

use Illuminate\Validation\Rule;

class CreateUserPreferencesRequest extends BaseUserPreferencesRequest
{
    public function getThemeRules(): array|string
    {
        return [
            "required",
            "string",
            Rule::in(explode(",", config("my_clinic.supported_theme_modes"))),
        ];
    }

    public function getLocaleRules(): array|string
    {
        return [
            "required",
            "string",
            Rule::in(explode(",", config("my_clinic.supported_locales"))),
        ];
    }
}
