<?php

namespace App\Services;

use App\Exceptions\DeleteAttemptOfNonExistingModelException;
use App\Exceptions\FailedToSaveObjectException;
use App\Exceptions\UpdateRequestForNonExistingObjectException;
use App\Models\User;
use App\Models\UserPreferences;
use App\Traits\ProvidesClassName;

class UserPreferencesService
{
    use ProvidesClassName;

    /**
     *
     * @param int|null $user_id
     * @param string|null $theme
     * @param string|null $locale
     * @return UserPreferences
     * @throws \App\Exceptions\FailedToSaveObjectException
     * @throws \App\Exceptions\UserNotFoundException
     * @throws \App\Exceptions\UserPreferencesAlreadyExistsException
     */
    public function store(
        ?int $user_id,
        string|null $theme,
        string|null $locale
    ): UserPreferences {
        User::checkIfExists($user_id);
        User::checkHasPreferences($user_id);

        $user_preferences = new UserPreferences();
        $user_preferences->user_id = $user_id;
        $user_preferences->theme = $theme;
        $user_preferences->locale = $locale;

        if ($user_preferences->save()) {
            return $user_preferences;
        } else {
            throw new FailedToSaveObjectException(self::className());
        }
    }

    /**
     * Update specified UserPreferences theme and/or locale
     *
     * @param \App\Models\UserPreferences|null $user_preferences
     * @param string|null $theme
     * @param string|null $locale
     * @return bool
     * @throws \App\Exceptions\UpdateRequestForNonExistingObjectException
     */
    public function update(
        ?UserPreferences $user_preferences,
        string|null $theme,
        string|null $locale
    ): bool {
        if ($user_preferences) {
            $user_preferences->theme = $theme ?? $user_preferences->theme;
            $user_preferences->locale = $locale ?? $user_preferences->locale;
            return $user_preferences->save();
        }
        throw new UpdateRequestForNonExistingObjectException();
    }

    /**
     *
     * @param \App\Models\UserPreferences|null $user_preferences
     * @return boolean
     * @throws \App\Exceptions\DeleteAttemptOfNonExistingModelException
     */
    public function delete(?UserPreferences $user_preferences): bool
    {
        if ($user_preferences) {
            return $user_preferences->delete();
        }
        throw new DeleteAttemptOfNonExistingModelException();
    }
}
