<?php

namespace Domain\UserPreferences\Traits;

use App\Models\User;
use Domain\UserPreferences\DataTransferObjects\UserPreferencesData;
use Domain\UserPreferences\Exceptions\ProvidedLocalePreferenceNotValidException;
use Domain\UserPreferences\Exceptions\ProvidedThemePreferenceNotValidException;
use Domain\Users\Exceptions\UserNotFoundException;

trait ValidatesUserPreferencesData
{
    /**
     * @throws UserNotFoundException
     * @throws ProvidedThemePreferenceNotValidException
     * @throws ProvidedLocalePreferenceNotValidException
     */
    public function validateData(
        UserPreferencesData $data,
        bool $forCreate
    ): void {
        $this->verifyUserExists($data->user_id);

        $this->verifyThemeIsValid(theme: $data->theme, required: $forCreate);
        $this->verifyLocaleIsValid(locale: $data->locale, required: $forCreate);
    }

    private function verifyUserExists(?int $user_id): void
    {
        if (
            !$user_id ||
            User::query()
                ->where("id", $user_id)
                ->doesntExist()
        ) {
            throw new UserNotFoundException($user_id);
        }
    }

    private function verifyThemeIsValid(?string $theme, bool $required): void
    {
        $supported_themes = explode(
            ",",
            config("my_clinic.supported_theme_modes")
        );
        if (
            $required &&
            (is_null($theme) ||
                !in_array(strtolower($theme), $supported_themes))
        ) {
            throw new ProvidedThemePreferenceNotValidException($theme);
        }
    }

    private function verifyLocaleIsValid(?string $locale, bool $required): void
    {
        $supported_locales = explode(
            ",",
            config("my_clinic.supported_locales")
        );
        if (
            $required &&
            (is_null($locale) ||
                !in_array(strtolower($locale), $supported_locales))
        ) {
            throw new ProvidedLocalePreferenceNotValidException($locale);
        }
    }
}
