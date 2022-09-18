<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserPreferences;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\UserPreferencesAlreadyExistsException;



class UserPreferencesService
{
    /**
     *
     * @param integer $user_id
     * @param string|null $theme
     * @param string|null $language
     * @return UserPreferences|null
     * @throws UserNotFoundException
     * @throws UserPreferencesAlreadyExistsException

     */
    public function store(int $user_id, string|null $theme, string|null $language): UserPreferences|null
    {
        User::checkIfExists($user_id);
        User::checkHasPreferences($user_id);

        $user_preferences = new UserPreferences();
        $user_preferences->user_id = $user_id;
        $user_preferences->theme = $theme;
        $user_preferences->language = $language;

        if ($user_preferences->save()) {
            return $user_preferences;
        }
    }
    /**
     * Update specified UserPreferences theme and/or language
     *
     * @param \App\Models\UserPreferences $user_preferences
     * @param string|null $theme
     * @param string|null $language
     * @return bool
     */
    public function update(UserPreferences $user_preferences, string|null $theme, string|null $language): bool
    {
        $user_preferences->theme = $theme ?? $user_preferences->theme;
        $user_preferences->language = $language ?? $user_preferences->language;
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
