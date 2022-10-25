<?php

namespace Domain\UserPreferences\Actions;

use App\Exceptions\FailedToSaveObjectException;
use App\Exceptions\UpdateRequestForNonExistingObjectException;
use Domain\UserPreferences\DataTransferObjects\UserPreferencesData;
use Domain\UserPreferences\Exceptions\ProvidedLocalePreferenceNotValidException;
use Domain\UserPreferences\Exceptions\ProvidedThemePreferenceNotValidException;
use Domain\UserPreferences\Factories\UserPreferencesDataFactory;
use Domain\UserPreferences\Models\UserPreferences;
use Domain\UserPreferences\Traits\ValidatesUserPreferencesData;
use Domain\Users\Exceptions\UserNotFoundException;

class UpdateUserPreferencesAction
{
    use ValidatesUserPreferencesData;

    /**
     * @throws FailedToSaveObjectException
     * @throws UpdateRequestForNonExistingObjectException
     * @throws ProvidedLocalePreferenceNotValidException
     * @throws ProvidedThemePreferenceNotValidException
     * @throws UserNotFoundException
     */
    public function execute(
        ?UserPreferences $user_preferences,
        UserPreferencesData $data
    ): bool {
        if ($user_preferences) {
            $this->validateData($data, forCreate: false);
            $user_preferences = $user_preferences->updateFromData($data);
            if (!$user_preferences->save()) {
                throw new FailedToSaveObjectException(UserPreferences::class);
            }
        } else {
            $data = UserPreferencesDataFactory::new()
                ->withLocale($data->locale)
                ->withTheme($data->theme)
                ->withUserId($data->user_id)
                ->create();
            app(CreateUserPreferencesAction::class)->execute($data);
        }
        return true;
    }
}
