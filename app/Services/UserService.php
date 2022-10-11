<?php

namespace App\Services;

use App\Exceptions\EmailAlreadyRegisteredException;
use App\Exceptions\PhoneNumberAlreadyUsedException;
use App\Exceptions\UnauthorizedToDeleteUserException;
use App\Exceptions\UserDoesntMatchHisStaffMemberException;
use App\Models\StaffMember;
use App\Models\User;
use App\Traits\ProvidesApiJsonResponse;
use App\Traits\UpdatesModelAttributes;
use Illuminate\Support\Facades\Hash;

class UserService
{
    use ProvidesApiJsonResponse, UpdatesModelAttributes;

    /**
     *
     * @param string $email
     * @param string $name
     * @param string $phone_number
     * @param string $password
     * @return \App\Models\User
     * @throws \App\Exceptions\EmailAlreadyRegisteredException
     * @throws \App\Exceptions\PhoneNumberAlreadyUsedException
     */
    public function createNewUser(
        string $email,
        string $name,
        string $phone_number,
        string $password
    ): User
    {
        $this->checkEmailAlreadyRegistered($email);
        $this->verifyPhoneNumberIsUnique($phone_number);
        return User::create([
            "email" => $email,
            "name" => $name,
            "phone_number" => $phone_number,
            "password" => Hash::make($password),
            "role_id" => StaffMember::whereEmail($email)->first(["role_id"])
                ->role_id,
        ]);
    }

    /**
     * @param string $email
     * @return void
     * @throws \App\Exceptions\EmailAlreadyRegisteredException
     */
    private function checkEmailAlreadyRegistered(string $email): void
    {
        if (User::whereEmail($email)->count() != 0) {
            throw new EmailAlreadyRegisteredException($email);
        }
    }

    /**
     * @param string $phone_number
     * @return void
     * @throws \App\Exceptions\PhoneNumberAlreadyUsedException
     */
    private function verifyPhoneNumberIsUnique(string $phone_number): void
    {
        if (User::where("phone_number", $phone_number)->count() != 0) {
            throw new PhoneNumberAlreadyUsedException($phone_number);
        }
    }

    /**
     * @param User $user
     * @param int|null $role_id
     * @param string|null $email
     * @param string|null $phone_number
     * @return \App\Models\User
     * @throws EmailAlreadyRegisteredException
     * @throws UserDoesntMatchHisStaffMemberException
     * @throws PhoneNumberAlreadyUsedException
     */
    public function update(
        User        $user,
        int|null    $role_id = null,
        string|null $email = null,
        string|null $phone_number = null
    ): User
    {
        if ($email && $user->email != $email) {
            $this->checkEmailAlreadyRegistered($email);
            $user->email = $email;
        }
        if ($role_id) {
            $user->role_id = $role_id;
        }
        if ($phone_number) {
            $this->verifyPhoneNumberIsUnique($phone_number);
            $user->phone_number = $phone_number;
        }
        // It's Important Before updating user's data in DB to verify it matches his
        // staff_member data(role-email)
        $this->verifyUserMatchHisStaffMember($user);
        return $this->performUpdate($user);
    }

    /**
     *  Verifies that new user's role and email matches those assigned to him in
     *  his StaffMember.
     * @param \App\Models\User $user
     * @param integer $new_role_id
     * @return void
     * @throws UserDoesntMatchHisStaffMemberException
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
     * @return \App\Models\User
     */
    private function performUpdate(User $user): User
    {
        if ($user->isDirty(["role_id", "email"])) {
            $user->tokens()->delete();
            $user->refreshTokens()->delete();
        }
        if ($user->isDirty("email")) {
            $user->email_verified_at = null;
        }
        $user->save();
        return $user;
    }

    /**
     *
     * @param User $user
     * @param User $request_user
     * @return bool|null
     * @throws \App\Exceptions\UnauthorizedToDeleteUserException
     **/
    public function delete(User $user, User $request_user): bool|null
    {
        $this->verifyCanDeleteUser($user, $request_user);

        $user->tokens()->delete();
        $user->refreshTokens()->delete();
        return $user->delete();
    }

    /**
     * Returns whether the user requesting the delete have the permission to delete
     * the specified user.
     *
     * @param User $user_to_delete
     * @param User $request_user
     * @throws \App\Exceptions\UnauthorizedToDeleteUserException
     */
    private function verifyCanDeleteUser(
        User $user_to_delete,
        User $request_user
    ): void
    {
        $is_same_user = $user_to_delete->id == $request_user->id;
        $is_admin = $request_user->tokenCan("admin");
        if (!($is_admin || $is_same_user)) {
            throw new UnauthorizedToDeleteUserException();
        }
    }
}
