<?php

namespace Domain\Users\Traits;

use App\Exceptions\EmailAlreadyRegisteredException;
use App\Exceptions\EmailUnauthorizedToRegisterException;
use App\Models\User;
use Domain\StaffMembers\Models\StaffMember;
use Domain\Users\DataTransferObjects\CreateUserData;
use Domain\Users\DataTransferObjects\UpdateUserData;
use Domain\Users\Exceptions\RoleNotFoundException;
use Domain\Users\Exceptions\UserDoesntMatchHisStaffMemberException;
use Domain\Users\Models\Role;

trait VerifiesUserData
{
    /**
     * @throws EmailUnauthorizedToRegisterException
     * @throws UserDoesntMatchHisStaffMemberException
     */
    private function verifyCreateUserDataMatchHisStaffMember(
        CreateUserData $data
    ): void {
        $user_staff_member = StaffMember::where("email", $data->email)->first();
        $has_same_role = $user_staff_member->role_id == $data->role_id;
        $has_same_email = $user_staff_member->email == $data->email;
        if (!($has_same_email && $has_same_role)) {
            throw new UserDoesntMatchHisStaffMemberException($data->email);
        }
    }

    private function verifyEmailNotAlreadyRegistered(
        string $email,
        ?string $exclude = null
    ): void {
        if (
            User::query()
                ->where("email", $email)
                ->whereNot("email", $exclude)
                ->exists()
        ) {
            throw new EmailAlreadyRegisteredException($email);
        }
    }

    /**
     * @throws EmailUnauthorizedToRegisterException
     */
    private function verifyIsStaffEmail(string $email): void
    {
        if (
            StaffMember::query()
                ->where("email", $email)
                ->doesntExist()
        ) {
            throw new EmailUnauthorizedToRegisterException($email);
        }
    }

    private function verifyRoleExists(int $role_id): void
    {
        if (
            Role::query()
                ->where("id", $role_id)
                ->doesntExist()
        ) {
            throw new RoleNotFoundException("role where id=$role_id");
        }
    }

    /**
     *  Verifies that new user's role and email matches those of
     *  his StaffMember.
     *
     * @throws UserDoesntMatchHisStaffMemberException
     */
    private function verifyUpdateUserDataMatchHisStaffMember(
        int $user_id,
        UpdateUserData $data
    ): void {
        $user_staff_member = StaffMember::where("user_id", $user_id)->first([
            "role_id",
            "email",
        ]);

        $has_same_email = $has_same_role = true;
        if ($data->role_id) {
            $has_same_role = $user_staff_member->role_id == $data->role_id;
        }
        if ($data->email) {
            $has_same_email = $user_staff_member->email == $data->email;
        }
        if (!($has_same_email && $has_same_role)) {
            throw new UserDoesntMatchHisStaffMemberException($data->email);
        }
    }
}
