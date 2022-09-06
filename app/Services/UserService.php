<?php

namespace App\Services;

use App\Exceptions\EmailAlreadyRegisteredException;
use App\Models\StaffEmail;
use App\Models\User;
use App\Traits\ProvidesApiJsonResponse;
use App\Traits\UpdatesModelAttributes;
use \Illuminate\Support\Facades\Hash;
use \Illuminate\Support\Facades\Validator;
use App\Models\StaffEmailUser;
use App\Exceptions\UnauthorizedToDeleteUserException;
use App\Exceptions\UserDoesntMatchHisStaffEmailException;

class UserService
{
    use ProvidesApiJsonResponse, UpdatesModelAttributes;
    /**
     * 
     * @param User $user
     * @param User $request_user
     * @return bool|null 
     * @throws \App\Exceptions\UnauthorizedToDeleteUserException
     **/
    public function deleteUser(User $user, User $request_user): bool|null
    {
        $this->verifyCanDeleteUser($user, $request_user);

        $user->tokens()->delete();
        return $user->delete();
    }
    /**
     * Returns whether the request_user have the permission to delete 
     * the specified user.  
     *
     * @param User $user_to_delete
     * @param User $request_user
     * @throws \App\Exceptions\UnauthorizedToDeleteUserException
     */
    private function verifyCanDeleteUser(User $user_to_delete, User $request_user)
    {
        $is_same_user = $user_to_delete->id == $request_user->id;
        $is_admin = $request_user->tokenCan('admin');
        if (!($is_admin || $is_same_user)) {
            throw new UnauthorizedToDeleteUserException();
        }
    }

    /**
     *
     * @param string $email
     * @param string $name
     * @param string $password
     * @return \App\Models\User
     * @throws \App\Exceptions\EmailAlreadyRegisteredException
     */
    public function createNewUser(string $email, string $name, string $password)
    {

        $this->checkEmailAlreadyRegistered($email);
        $user =  User::create([
            'email' => $email,
            'name' => $name,
            'password' => Hash::make($password),
            'role_id' => StaffEmail::whereEmail($email)->first('role_id')->role_id,
        ]);
        //
        StaffEmailUser::create([
            'user_id' => $user->id,
            'staff_email_id' => StaffEmail::whereEmail($user->email)->first()->id,
        ]);

        return $user;
    }


    /**
     * @param User $user
     * @param int|null $role_id
     * @param string|null $email
     * @return \App\Models\User
     * @throws EmailAlreadyRegisteredException|UserDoesntMatchHisStaffEmailException
     */
    public function update(User $user, int|null $role_id = null, string|null $email = null): User
    {
        if ($email && $user->email != $email) {
            $this->checkEmailAlreadyRegistered($email);
            $user->email = $email;
        }
        if ($role_id) {
            $user->role_id = $role_id;
        }
        $this->verifyUserMatchHisStaffEmail($user);
        return $this->performUpdate($user);
    }

    /**
     * @param \App\Models\User $user
     * @return \App\Models\User
     */
    private function performUpdate(User $user): User
    {
        if ($user->isDirty(['role_id', 'email'])) {
            $user->tokens()->delete();
        }
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();
        return $user;
    }
    /**
     * @param string $email
     * @return void
     * @throws \App\Exceptions\EmailAlreadyRegisteredException
     */
    private function checkEmailAlreadyRegistered(string $email)
    {
        if (User::whereEmail($email)->count() != 0) {
            throw new EmailAlreadyRegisteredException($email);
        }
    }
    /**
     *
     * @param \App\Models\User $user
     * @param integer $new_role_id
     * @return void
     * @throws UserDoesntMatchHisStaffEmailException
     */
    private function verifyUserMatchHisStaffEmail(User $user)
    {
        $has_same_role_id = $user->staffEmail()->get(['role_id'])->first()->role_id == $user->role_id;;
        $has_same_email = $user->staffEmail()->get(['email'])->first()->email == $user->email;
        if (!($has_same_email && $has_same_role_id)) {
            throw new UserDoesntMatchHisStaffEmailException($user);
        }
    }
}
