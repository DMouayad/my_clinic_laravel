<?php

namespace Domain\UserPreferences\DataTransferObjects;

class UserPreferencesData
{
    private function __construct(
        readonly ?int $user_id,
        readonly ?string $theme,
        readonly ?string $locale
    ) {
    }

    public static function forCreate(
        int $user_id,
        string $theme,
        string $locale
    ): self {
        return new UserPreferencesData(
            user_id: $user_id,
            theme: $theme,
            locale: $locale
        );
    }

    public static function forUpdate(
        int $user_id,
        ?string $theme = null,
        ?string $locale = null
    ): self {
        return new UserPreferencesData(
            user_id: $user_id,
            theme: $theme,
            locale: $locale
        );
    }
}
