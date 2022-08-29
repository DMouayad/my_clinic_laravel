<?php

namespace App\Services;

use App\Exceptions\DeletingOnlyAdminStaffEmailException;
use App\Models\Role;
use App\Models\StaffEmail;
use App\Services\UserService;
use App\Exceptions\StaffEmailAlreadyExistsException;

class StaffEmailService
{
    /**
     * @param string $email
     * @param string $role_slug
     * @return \App\Models\StaffEmail
     * @throws \App\Exceptions\StaffEmailAlreadyExistsException|\App\Exceptions\RoleNotFoundException
     */
    public function store(string $email, string $role_slug): StaffEmail
    {
        $this->checkIfStaffEmailExists($email);
        return $this->createStaffEmail($email, $role_slug);
    }

    /**
     * Check if the passed email address already exists in the database.
     *
     * @param string $email
     * @return void
     * @throws \App\Exceptions\StaffEmailAlreadyExistsException
     */
    public function checkIfStaffEmailExists(string $email): void
    {
        if (StaffEmail::whereEmail($email)->exists()) {
            throw new StaffEmailAlreadyExistsException($email);
        }
    }
    /**
     * Creates new StaffEmail with provided params.
     * returns JsonResponse of the creation process.
     *
     * @param string $email
     * @param string $role_slug
     * @return \App\Models\StaffEmail
     * @throws \App\Exceptions\RoleNotFoundException
     */
    private function createStaffEmail(string $email, string $role_slug): StaffEmail
    {
        $role_id = Role::getIdBySlug($role_slug);
        return StaffEmail::create(['email' => $email, 'role_id' => $role_id,]);
    }

    /**
     * Update the specified staff_email.
     *
     * @param  string|null  $email
     * @param  string|null $role_slug
     * @param  \App\Models\StaffEmail  $staff_email
     * @return bool
     * @throws \App\Exceptions\RoleNotFoundException|\App\Exceptions\StaffEmailAlreadyExistsException
     */
    public function update(
        string|null  $email,
        string|null $role_slug,
        StaffEmail $staff_email,
        UserService $userService,
    ): bool {
        if ($staff_email->email != $email) {
            $this->checkIfStaffEmailExists($email);
        }

        if ($email) {
            $staff_email->email = $email;
        }
        if ($role_slug) {
            $staff_email->role_id = Role::getIdBySlug($role_slug);
        }
        if ($staff_email->isDirty()) {
            $updated = $staff_email->save();

            $updated_user = $userService->update(
                $staff_email->user,
                ['email' => $staff_email->email, 'role_id' => $staff_email->role_id]
            );
            return $updated && $staff_email->user ==  $updated_user;
        }
        return true;
    }

    /**
     * Delete provided staffEmail
     * @param StaffEmail $staff_email
     * @return bool
     * @throws \App\Exceptions\DeletingOnlyAdminStaffEmailException
     */
    public function delete(StaffEmail $staff_email): bool
    {
        $this->checkCanBeDeletedIfAdmin($staff_email);
        return $this->performDelete($staff_email);
    }
    /**
     * Check if staff_email can be deleted in case of an Admin's staffEmail
     *
     * @param StaffEmail $staff_email
     * @return void
     * @throws \App\Exceptions\DeletingOnlyAdminStaffEmailException
     */
    private function checkCanBeDeletedIfAdmin(StaffEmail $staff_email): void
    {
        $admin_role_id = Role::getIdBySlug('admin');
        if ($staff_email->role_id == $admin_role_id) {

            $numberOfAdminEmails = StaffEmail::where('role_id', $admin_role_id)->count();
            if ($numberOfAdminEmails == 1) {
                throw new DeletingOnlyAdminStaffEmailException();
            }
        }
    }

    /**
     * Delete the specified staffEmail with its user
     * @param StaffEmail $staff_email
     * @return boolean
     */
    private function performDelete(StaffEmail $staff_email): bool
    {
        // NOTE:
        // Deleting the user before deleting the staffEmail is IMPORTANT due to:
        // - having onDeleteCascade constraints on the 'staff_email_user' table 
        // which deletes the relationship between the user and staffEmail 
        $staff_email->user()->delete();
        return $staff_email->delete();
    }
}
