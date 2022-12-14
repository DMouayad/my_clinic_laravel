<?php

namespace Domain\StaffMembers\Actions;

use App\Exceptions\FailedToDeleteObjectException;
use App\Exceptions\UnauthorizedToDeleteUserException;
use App\Models\User;
use Domain\StaffMembers\Exceptions\DeletingOnlyAdminStaffMemberException;
use Domain\StaffMembers\Models\StaffMember;
use Domain\Users\Actions\DeleteUserAction;
use Domain\Users\Events\UserWasDeleted;
use Domain\Users\Exceptions\RoleNotFoundException;
use Domain\Users\Models\Role;

class DeleteStaffMemberAction
{
    public function __construct(
        private readonly DeleteUserAction $deleteUserAction
    ) {
    }

    /**
     * @throws RoleNotFoundException
     * @throws FailedToDeleteObjectException
     * @throws UnauthorizedToDeleteUserException
     * @throws DeletingOnlyAdminStaffMemberException
     */
    public function execute(StaffMember $staff_member, User $request_user): bool
    {
        $this->checkCanBeDeletedIfIsAdmin($staff_member);
        if ($staff_member->user) {
            $this->deleteUserAction->execute(
                $staff_member->user,
                $request_user
            );
            event(new UserWasDeleted($staff_member->user));
        }
        if (!$staff_member->delete()) {
            throw new FailedToDeleteObjectException(StaffMember::class);
        }
        return true;
    }

    /**
     * Check if [staff_member] can be deleted in case of a staffMember with admin role.
     *
     * @param  StaffMember  $staff_member
     *
     * @return void
     * @throws DeletingOnlyAdminStaffMemberException
     * @throws RoleNotFoundException
     */
    private function checkCanBeDeletedIfIsAdmin(StaffMember $staff_member): void
    {
        $admin_role_id = Role::getIdWhereSlug("admin");
        if ($staff_member->role_id == $admin_role_id) {
            $numberOfAdmins = StaffMember::where(
                "role_id",
                $admin_role_id
            )->count();
            if ($numberOfAdmins == 1) {
                throw new DeletingOnlyAdminStaffMemberException();
            }
        }
    }
}
