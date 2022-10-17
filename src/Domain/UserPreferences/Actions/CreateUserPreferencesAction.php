<?php

namespace Domain\UserPreferences\Actions;

use App\Exceptions\FailedToSaveObjectException;
use Domain\UserPreferences\DataTransferObjects\UserPreferencesData;
use Domain\UserPreferences\Models\UserPreferences;

class CreateUserPreferencesAction
{
    public function __construct(
        private readonly UpdateUserPreferencesAction $updateUserPreferencesAction
    ) {
    }

    /**
     * @throws \App\Exceptions\FailedToSaveObjectException
     */
    public function execute(UserPreferencesData $data): UserPreferences
    {
        if ($this->user_has_preferences($data->user_id)) {
            $preferences = UserPreferences::whereUserId($data->user_id);
            $this->updateUserPreferencesAction->execute($preferences, $data);
            return $preferences->refresh();
        } else {
            return $this->create_preferences($data);
        }
    }

    private function user_has_preferences(int $user_id): bool
    {
        return UserPreferences::query()
            ->where("user_id", $user_id)
            ->exists();
    }

    private function create_preferences(
        UserPreferencesData $data
    ): UserPreferences {
        $user_preferences = new UserPreferences();
        $user_preferences->user_id = $data->user_id;
        $user_preferences->theme = $data->theme;
        $user_preferences->locale = $data->locale;

        if (!$user_preferences->save()) {
            throw new FailedToSaveObjectException(UserPreferences::class);
        }
        return $user_preferences;
    }
}
