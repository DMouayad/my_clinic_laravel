<?php

namespace Domain\StaffMembers\Actions;

use App\Exceptions\FailedToSaveObjectException;
use Domain\StaffMembers\DataTransferObjects\StaffMemberData;
use Domain\StaffMembers\Exceptions\StaffMemberAlreadyExistsException;
use Domain\StaffMembers\Models\StaffMember;
use Domain\StaffMembers\Traits\VerifiesStaffMemberIsUnique;
use Domain\Users\Exceptions\RoleNotFoundException;
use Domain\Users\Models\Role;

class AddStaffMemberAction
{
    use VerifiesStaffMemberIsUnique;

    /**
     * @throws FailedToSaveObjectException
     * @throws RoleNotFoundException
     * @throws StaffMemberAlreadyExistsException
     */
    public function execute(StaffMemberData $data): StaffMember
    {
        $this->verifyEmailIsUnique(email: $data->email);
        $staff_member = new StaffMember();
        $staff_member->email = $data->email;
        $staff_member->role_id = Role::getIdWhereSlug($data->role);

        if (!$staff_member->save()) {
            throw new FailedToSaveObjectException(StaffMember::class);
        }
        return $staff_member;
    }
}
