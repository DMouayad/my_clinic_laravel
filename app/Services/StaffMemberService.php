<?php

namespace App\Services;

use App\Exceptions\DeletingOnlyAdminStaffMemberException;
use App\Exceptions\FailedToDeleteObjectException;
use App\Exceptions\FailedToSaveObjectException;
use App\Exceptions\FailedToUpdateObjectException;
use App\Exceptions\StaffMemberAlreadyExistsException;
use App\Models\Role;
use App\Models\StaffMember;

class StaffMemberService
{
    /**
     * @param string $email
     * @param string $role_slug
     * @return \App\Models\StaffMember
     * @throws \App\Exceptions\FailedToSaveObjectException
     * @throws \App\Exceptions\RoleNotFoundException
     * @throws \App\Exceptions\StaffMemberAlreadyExistsException
     */
    public function store(string $email, string $role_slug): StaffMember
    {
        $this->checkIfStaffMemberExists($email);
        return $this->createStaffMember($email, $role_slug);
    }

    /**
     * Check if the passed email address already exists in the database.
     * @param string $email
     * @return void
     * @throws \App\Exceptions\StaffMemberAlreadyExistsException
     */
    public function checkIfStaffMemberExists(string $email): void
    {
        if (StaffMember::whereEmail($email)->exists()) {
            throw new StaffMemberAlreadyExistsException($email);
        }
    }

    /**
     * Creates new StaffMember with provided params.
     *
     * @param string $email
     * @param string $role_slug
     * @return StaffMember
     * @throws \App\Exceptions\FailedToSaveObjectException
     * @throws \App\Exceptions\RoleNotFoundException
     */
    private function createStaffMember(
        string $email,
        string $role_slug
    ): StaffMember
    {
        $staff_member = new StaffMember();
        $staff_member->email = $email;
        $staff_member->role_id = Role::getIdBySlug($role_slug);

        if (!$staff_member->save()) {
            throw new FailedToSaveObjectException(StaffMember::class);
        }
        return $staff_member;
    }

    /**
     * Update the specified staff_member.
     *
     * @param \App\Models\StaffMember $staff_member
     * @param string|null $email
     * @param string|null $role_slug
     * @return \App\Models\StaffMember
     * @throws \App\Exceptions\FailedToUpdateObjectException
     * @throws \App\Exceptions\RoleNotFoundException
     * @throws \App\Exceptions\StaffMemberAlreadyExistsException
     */
    public function update(
        StaffMember $staff_member,
        string|null $email = null,
        string|null $role_slug = null
    ): StaffMember
    {
        // if not the same email was provided check if it already exists in the db
        if ($email && $staff_member->email != $email) {
            $this->checkIfStaffMemberExists($email);
            $staff_member->email = $email;
        }
        if ($role_slug) {
            $staff_member->role_id = Role::getIdBySlug($role_slug);
        }
        if ($staff_member->isDirty()) {
            if (!$staff_member->save()) {
                throw new FailedToUpdateObjectException(StaffMember::class);
            }
        }
        return $staff_member;
    }

    /**
     * Deletes specified staffMember
     * @param StaffMember $staff_member
     * @return bool
     * @throws \App\Exceptions\DeletingOnlyAdminStaffMemberException
     * @throws \App\Exceptions\FailedToDeleteObjectException
     * @throws \App\Exceptions\RoleNotFoundException
     */
    public function delete(StaffMember $staff_member): bool
    {
        $this->checkCanBeDeletedIfAdmin($staff_member);
        return $this->performDelete($staff_member);
    }

    /**
     * Check if staff_member can be deleted in case of an Admin's staffMember
     *
     * @param StaffMember $staff_member
     * @return void
     * @throws \App\Exceptions\DeletingOnlyAdminStaffMemberException
     * @throws \App\Exceptions\RoleNotFoundException
     */
    private function checkCanBeDeletedIfAdmin(StaffMember $staff_member): void
    {
        $admin_role_id = Role::getIdBySlug("admin");
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

    /**
     * Delete the specified staffMember
     * @param StaffMember $staff_member
     * @return boolean
     * @throws \App\Exceptions\FailedToDeleteObjectException
     */
    private function performDelete(StaffMember $staff_member): bool
    {
        if (!$staff_member->delete()) {
            throw new FailedToDeleteObjectException(StaffMember::class);
        }
        return true;
    }
}
