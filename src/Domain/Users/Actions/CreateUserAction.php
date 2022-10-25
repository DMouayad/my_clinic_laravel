<?php

namespace Domain\Users\Actions;

use App\Exceptions\EmailAlreadyRegisteredException;
use App\Exceptions\EmailUnauthorizedToRegisterException;
use App\Exceptions\FailedToUpdateObjectException;
use App\Models\User;
use Domain\StaffMembers\Actions\UpdateStaffMemberAction;
use Domain\StaffMembers\DataTransferObjects\StaffMemberData;
use Domain\StaffMembers\Exceptions\StaffMemberAlreadyExistsException;
use Domain\StaffMembers\Models\StaffMember;
use Domain\Users\DataTransferObjects\CreateUserData;
use Domain\Users\Exceptions\RoleNotFoundException;
use Domain\Users\Exceptions\UserDoesntMatchHisStaffMemberException;
use Domain\Users\Traits\VerifiesUserData;
use Illuminate\Support\Facades\Hash;

class CreateUserAction
{
    use VerifiesUserData;

    public function __construct(
        private readonly UpdateStaffMemberAction $updateStaffMemberAction
    ) {
    }

    /**
     * @throws FailedToUpdateObjectException
     * @throws UserDoesntMatchHisStaffMemberException
     * @throws StaffMemberAlreadyExistsException
     * @throws EmailAlreadyRegisteredException
     * @throws RoleNotFoundException
     * @throws EmailUnauthorizedToRegisterException
     */
    public function execute(CreateUserData $data): User
    {
        $this->verifyIsStaffEmail($data->email);
        $this->verifyEmailNotAlreadyRegistered($data->email);
        $this->verifyRoleExists($data->role_id);
        $this->verifyCreateUserDataMatchHisStaffMember($data);

        $user = new User();
        $user->email = $data->email;
        $user->name = $data->name;
        $user->phone_number = $data->phone_number;
        $user->password = Hash::make($data->password);
        $user->role_id = $data->role_id;
        $user->save();
        $user->refresh();
        // set  [user_id] of the staffMember with same email
        $this->updateStaffMemberAction->execute(
            staff_member: StaffMember::findWhereEmail($user->email),
            data: StaffMemberData::forUpdate(user_id: $user->id)
        );
        return $user;
    }
}
