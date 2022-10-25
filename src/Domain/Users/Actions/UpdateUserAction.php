<?php

namespace Domain\Users\Actions;

use App\Exceptions\EmailAlreadyRegisteredException;
use App\Exceptions\EmailUnauthorizedToRegisterException;
use App\Exceptions\FailedToUpdateObjectException;
use App\Models\User;
use Domain\Users\DataTransferObjects\UpdateUserData;
use Domain\Users\Exceptions\RoleNotFoundException;
use Domain\Users\Exceptions\UserDoesntMatchHisStaffMemberException;
use Domain\Users\Traits\VerifiesUserData;

class UpdateUserAction
{
    use VerifiesUserData;

    /**
     * @throws FailedToUpdateObjectException
     * @throws UserDoesntMatchHisStaffMemberException
     * @throws EmailAlreadyRegisteredException
     * @throws RoleNotFoundException
     * @throws EmailUnauthorizedToRegisterException
     */
    public function execute(User $user, UpdateUserData $data): bool
    {
        if ($data->email) {
            $this->verifyIsStaffEmail($data->email);
            $this->verifyEmailNotAlreadyRegistered(
                email: $data->email,
                exclude: $user->email
            );
        }
        $this->verifyRoleExists($data->role_id);
        // It's Important, Before updating user's data in the DB, to verify it matches his
        // StaffMember's
        $this->verifyUpdateUserDataMatchHisStaffMember($user->id, $data);
        return $this->performUpdate($user, $data);
    }

    /**
     * @throws FailedToUpdateObjectException
     */
    private function performUpdate(User $user, UpdateUserData $data): bool
    {
        $user = $user->updateFromUserData($data);

        if ($user->isDirty(["role_id", "email"])) {
            $user->tokens()->delete();
            $user->refreshTokens()->delete();
        }
        if ($user->isDirty("email")) {
            $user->email_verified_at = null;
        }
        $updated = $user->save();
        if (!$updated) {
            throw new FailedToUpdateObjectException(User::class);
        }
        return $updated;
    }
}
