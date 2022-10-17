<?php

namespace Domain\StaffMembers\Actions;

use App\Exceptions\FailedToUpdateObjectException;
use Domain\StaffMembers\DataTransferObjects\StaffMemberData;
use Domain\StaffMembers\Models\StaffMember;
use Domain\Users\Actions\UpdateUserAction;
use Domain\Users\DataTransferObjects\UpdateUserData;

class UpdateStaffMemberAction
{
    public function __construct(
        private readonly UpdateUserAction $updateUserAction
    ) {
    }

    /**
     * @throws \App\Exceptions\FailedToUpdateObjectException
     * @throws \Domain\Users\Exceptions\UserDoesntMatchHisStaffMemberException
     */
    private function updateStaffMemberUser(StaffMember $staff_member): void
    {
        if ($staff_member->user) {
            $this->updateUserAction->execute(
                $staff_member->user,
                UpdateUserData::fromStaffMember(
                    email: $staff_member->email,
                    role_id: $staff_member->role_id
                )
            );
        }
    }

    /**
     * Updates the StaffMember with provided data.
     *
     * @param \Domain\StaffMembers\Models\StaffMember $staff_member
     * @param \Domain\StaffMembers\DataTransferObjects\StaffMemberData $data
     * @return boolean
     * @throws \App\Exceptions\FailedToUpdateObjectException
     * @throws \Domain\Users\Exceptions\UserDoesntMatchHisStaffMemberException
     */
    public function execute(
        StaffMember $staff_member,
        StaffMemberData $data
    ): bool {
        $staff_member = $staff_member->updateFromData($data);
        $was_updated = $staff_member->save();
        if (!$was_updated) {
            throw new FailedToUpdateObjectException(StaffMember::class);
        }
        $this->updateStaffMemberUser($staff_member);
        return $was_updated;
    }
}
