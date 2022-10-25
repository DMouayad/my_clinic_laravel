<?php

namespace Domain\StaffMembers\Actions;

use App\Exceptions\EmailAlreadyRegisteredException;
use App\Exceptions\FailedToUpdateObjectException;
use Domain\StaffMembers\DataTransferObjects\StaffMemberData;
use Domain\StaffMembers\Exceptions\StaffMemberAlreadyExistsException;
use Domain\StaffMembers\Models\StaffMember;
use Domain\StaffMembers\Traits\VerifiesStaffMemberIsUnique;
use Domain\Users\Actions\UpdateUserAction;
use Domain\Users\DataTransferObjects\UpdateUserData;
use Domain\Users\Exceptions\RoleNotFoundException;
use Domain\Users\Exceptions\UserDoesntMatchHisStaffMemberException;

class UpdateStaffMemberAction
{
    use VerifiesStaffMemberIsUnique;

    public function __construct(
        private readonly UpdateUserAction $updateUserAction
    ) {
    }

    /**
     * @throws EmailAlreadyRegisteredException
     * @throws FailedToUpdateObjectException
     * @throws RoleNotFoundException
     * @throws StaffMemberAlreadyExistsException
     * @throws UserDoesntMatchHisStaffMemberException
     */
    public function execute(
        StaffMember $staff_member,
        StaffMemberData $data
    ): bool {
        if ($data->email) {
            $this->verifyEmailIsUnique(
                email: $data->email,
                exclude: $staff_member->email
            );
        }

        $staff_member = $staff_member->updateFromData($data);
        $was_updated = $staff_member->save();

        if (!$was_updated) {
            throw new FailedToUpdateObjectException(StaffMember::class);
        }
        // update user data only if [email, role_id] were changed - and not in case
        // of updating StaffMember's [user_id]
        if ($staff_member->wasChanged(["email", "role_id"])) {
            $this->updateStaffMemberUser($staff_member);
        }

        return $was_updated;
    }

    /**
     * @throws FailedToUpdateObjectException
     * @throws UserDoesntMatchHisStaffMemberException
     * @throws EmailAlreadyRegisteredException
     * @throws RoleNotFoundException
     */
    private function updateStaffMemberUser(StaffMember $staff_member): void
    {
        if ($staff_member->user) {
            $this->updateUserAction->execute(
                $staff_member->user,
                new UpdateUserData(
                    email: $staff_member->email,
                    role_id: $staff_member->role_id
                )
            );
        }
    }
}
