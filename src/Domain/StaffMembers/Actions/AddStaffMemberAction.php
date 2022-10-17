<?php

namespace Domain\StaffMembers\Actions;

use App\Exceptions\FailedToSaveObjectException;
use Domain\StaffMembers\DataTransferObjects\StaffMemberData;
use Domain\StaffMembers\Models\StaffMember;
use Domain\Users\Models\Role;

class AddStaffMemberAction
{
    /**
     * Creates new StaffMember with provided data.
     *
     * @param \Domain\StaffMembers\DataTransferObjects\StaffMemberData $data
     * @return StaffMember
     * @throws \App\Exceptions\FailedToSaveObjectException
     * @throws \Domain\Users\Exceptions\RoleNotFoundException
     */
    public function execute(StaffMemberData $data): StaffMember
    {
        $staff_member = new StaffMember();
        $staff_member->email = $data->email;
        $staff_member->role_id = Role::getIdWhereSlug($data->role);

        if (!$staff_member->save()) {
            throw new FailedToSaveObjectException(StaffMember::class);
        }
        return $staff_member;
    }
}
