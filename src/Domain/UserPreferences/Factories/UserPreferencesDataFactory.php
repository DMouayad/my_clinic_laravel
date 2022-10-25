<?php

namespace Domain\UserPreferences\Factories;

use App\Models\User;
use Domain\UserPreferences\DataTransferObjects\UserPreferencesData;
use Support\Factories\BaseFactory;

class UserPreferencesDataFactory extends BaseFactory
{
    private ?string $theme = null,
        $locale = null;
    private ?int $user_id = null;

    public function create(): UserPreferencesData
    {
        $this->theme ??= $this->faker()->randomElement(
            explode(",", config("my_clinic.supported_theme_modes"))
        );
        $this->locale ??= $this->faker()->randomElement(
            explode(",", config("my_clinic.supported_locales"))
        );
        $this->user_id ??= User::query()->first(["id"])->id;
        return UserPreferencesData::forCreate(
            user_id: $this->user_id,
            theme: $this->theme,
            locale: $this->locale
        );
    }

    public function createWithNullAttributes(): UserPreferencesData
    {
        return UserPreferencesData::forUpdate(user_id: null);
    }

    public function forUpdate(): UserPreferencesData
    {
        return UserPreferencesData::forUpdate(
            user_id: $this->user_id,
            theme: $this->theme,
            locale: $this->locale
        );
    }

    static function new(): self
    {
        return new self();
    }

    public function withLocale(?string $locale): self
    {
        $clone = clone $this;
        $clone->locale = $locale;
        return $clone;
    }

    public function withTheme(?string $theme): self
    {
        $clone = clone $this;
        $clone->theme = $theme;
        return $clone;
    }

    public function withUserId(?int $user_id): self
    {
        $clone = clone $this;
        $clone->user_id = $user_id;
        return $clone;
    }
}
