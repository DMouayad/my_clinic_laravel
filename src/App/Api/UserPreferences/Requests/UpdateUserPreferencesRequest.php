<?php

namespace App\Api\UserPreferences\Requests;

use Illuminate\Validation\Rule;

class UpdateUserPreferencesRequest extends BaseUserPreferencesRequest
{
    public function getThemeRules(): array|string
    {
        return [
            "required_without:locale",
            "string",
            Rule::in(explode(",", config("my_clinic.supported_theme_modes"))),
        ];
    }

    public function getLocaleRules(): array|string
    {
        return [
            "required_without:theme",
            "string",
            Rule::in(explode(",", config("my_clinic.supported_locales"))),
        ];
    }
}
