<?php

    namespace Domain\UserPreferences\Actions;

    use App\Exceptions\DeleteAttemptOfNonExistingModelException;
    use App\Exceptions\FailedToDeleteObjectException;
    use Domain\UserPreferences\Models\UserPreferences;

    class DeleteUserPreferencesAction
    {
        /**
         * @throws FailedToDeleteObjectException
         * @throws DeleteAttemptOfNonExistingModelException
         */
        public function execute(?UserPreferences $user_preferences): bool{
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
