<?php

namespace App\Services;

use App\Exceptions\UserNotFoundException;
use App\Exceptions\UserPreferencesAlreadyExistsException;
use App\Models\User;
use App\Models\UserPreferences;

class UserPreferencesService
{
    /**
     *
     * @param integer $user_id
     * @param string|null $theme
     * @param string|null $locale
     * @return UserPreferences|null
     * @throws UserNotFoundException
     * @throws UserPreferencesAlreadyExistsException
     */
    public function store(
        int $user_id,
        string|null $theme,
        string|null $locale
    ): UserPreferences|null {
        User::checkIfExists($user_id);
        User::checkHasPreferences($user_id);

        $user_preferences = new UserPreferences();
        $user_preferences->user_id = $user_id;
        $user_preferences->theme = $theme;
        $user_preferences->locale = $locale;

        if ($user_preferences->save()) {
            return $user_preferences;
        }
        return null;
    }

    /**
     * Update specified UserPreferences theme and/or locale
     *
     * @param \App\Models\UserPreferences $user_preferences
     * @param string|null $theme
     * @param string|null $locale
     * @return bool
     */
    public function update(
        UserPreferences $user_preferences,
        string|null $theme,
        string|null $locale
    ): bool {
        $user_preferences->theme = $theme ?? $user_preferences->theme;
        $user_preferences->locale = $locale ?? $user_preferences->locale;
        return $user_preferences->save();
    }

    /**
     *
     * @param \App\Models\UserPreferences $user_preferences
     * @return boolean
     */
    public function delete(UserPreferences $user_preferences): bool
    {
        return $user_preferences->delete();
    }
}
