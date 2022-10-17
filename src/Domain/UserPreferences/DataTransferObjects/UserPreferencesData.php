<?php

namespace Domain\UserPreferences\DataTransferObjects;

class UserPreferencesData
{
    private function __construct(
        readonly ?string $theme,
        readonly ?string $locale,
        readonly ?int $user_id
    ) {
    }

    public static function forCreate(
        string $theme,
        string $locale,
        int $user_id
    ): self {
        return new UserPreferencesData($theme, $locale, $user_id);
    }

    public static function forUpdate(
        ?string $theme = null,
        ?string $locale = null,
        ?int $user_id = null
    ): self {
        return new UserPreferencesData($theme, $locale, $user_id);
    }
}
