<?php

namespace App\Api\UserPreferences\Requests;

use Illuminate\Validation\Rule;

class AddUserPreferencesRequest extends BaseUserPreferencesRequest
{
    public function getThemeRules(): array|string
    {
        $supported_themes = explode(
            ",",
            config("my_clinic.supported_theme_modes")
        );
        return ["required", "string", Rule::in($supported_themes)];
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
