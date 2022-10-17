<?php

namespace Domain\UserPreferences\Actions;

use App\Exceptions\FailedToSaveObjectException;
use App\Exceptions\UpdateRequestForNonExistingObjectException;
use Domain\UserPreferences\DataTransferObjects\UserPreferencesData;
use Domain\UserPreferences\Models\UserPreferences;

class UpdateUserPreferencesAction
{
    /**
     * @throws \App\Exceptions\FailedToSaveObjectException
     * @throws \App\Exceptions\UpdateRequestForNonExistingObjectException
     */
    public function execute(
        ?UserPreferences $user_preferences,
        UserPreferencesData $data
    ): bool {
        if ($user_preferences) {
            $user_preferences = $user_preferences->updateFromData($data);
            if (!$user_preferences->save()) {
                throw new FailedToSaveObjectException(UserPreferences::class);
            }
            return true;
        } else {
            throw new UpdateRequestForNonExistingObjectException();
        }
    }
}
