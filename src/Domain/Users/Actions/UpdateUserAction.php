<?php

namespace Domain\Users\Actions;

use App\Exceptions\FailedToUpdateObjectException;
use App\Models\User;
use Domain\StaffMembers\Models\StaffMember;
use Domain\Users\DataTransferObjects\UpdateUserData;
use Domain\Users\Exceptions\UserDoesntMatchHisStaffMemberException;

class UpdateUserAction
{
    /**
     * @throws \Domain\Users\Exceptions\UserDoesntMatchHisStaffMemberException
     * @throws \App\Exceptions\FailedToUpdateObjectException
     */
    public function execute(User $user, UpdateUserData $data): bool
    {
        $user = $user->updateFromUserData($data);
        // It's Important, Before updating user's data in the DB, to verify it matches his
        // StaffMember's
        $this->verifyUserMatchHisStaffMember($user);
        return $this->performUpdate($user);
    }

    /**
     *  Verifies that new user's role and email matches those assigned to him in
     *  his StaffMember.
     * @param \App\Models\User $user
     * @return void
     * @throws \Domain\Users\Exceptions\UserDoesntMatchHisStaffMemberException
     */
    private function verifyUserMatchHisStaffMember(User $user): void
    {
        $user_staff_member = StaffMember::where("user_id", $user->id)->first([
            "role_id",
            "email",
        ]);
        $has_same_role = $user_staff_member->role_id == $user->role_id;
        $has_same_email = $user_staff_member->email == $user->email;
        if (!($has_same_email && $has_same_role)) {
            throw new UserDoesntMatchHisStaffMemberException($user);
        }
    }

    /**
     * @param \App\Models\User $user
     * @return boolean
     * @throws \App\Exceptions\FailedToUpdateObjectException
     */
    private function performUpdate(User $user): bool
    {
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
