<?php

namespace App\Services;

use App\Exceptions\DeleteAttemptOfNonExistingModelException;
use App\Exceptions\FailedToDeleteObjectException;
use App\Exceptions\FailedToSaveObjectException;
use App\Exceptions\UpdateRequestForNonExistingObjectException;
use App\Models\User;
use App\Models\UserPreferences;

class UserPreferencesService
{
    /**
     *
     * @param int|null $user_id
     * @param string|null $theme
     * @param string|null $locale
     * @return \App\Models\UserPreferences
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

        if (!$user_preferences->save()) {
            throw new FailedToSaveObjectException(UserPreferences::class);
        }
        return $user_preferences;
    }

    /**
     * Update specified UserPreferences theme and/or locale
     *
     * @param \App\Models\UserPreferences|null $user_preferences
     * @param string|null $theme
     * @param string|null $locale
     * @return \App\Models\UserPreferences
     * @throws \App\Exceptions\FailedToSaveObjectException
     * @throws \App\Exceptions\UpdateRequestForNonExistingObjectException
     */
    public function update(
        ?UserPreferences $user_preferences,
        string|null $theme,
        string|null $locale
    ): UserPreferences {
        if ($user_preferences) {
            $user_preferences->theme = $theme ?? $user_preferences->theme;
            $user_preferences->locale = $locale ?? $user_preferences->locale;

            if (!$user_preferences->save()) {
                throw new FailedToSaveObjectException(UserPreferences::class);
            }
            return $user_preferences;
        } else {
            throw new UpdateRequestForNonExistingObjectException();
        }
    }

    /**
     *
     * @param \App\Models\UserPreferences|null $user_preferences
     * @return boolean
     * @throws \App\Exceptions\DeleteAttemptOfNonExistingModelException
     * @throws \App\Exceptions\FailedToDeleteObjectException
     */
    public function delete(?UserPreferences $user_preferences): bool
    {
        if ($user_preferences) {
            $was_deleted = $user_preferences->delete();
            if (!$was_deleted) {
                throw new FailedToDeleteObjectException(UserPreferences::class);
            }
            return $was_deleted;
        } else {
            throw new DeleteAttemptOfNonExistingModelException();
        }
    }
}
