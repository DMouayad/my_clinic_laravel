<?php

namespace Domain\Users\DataTransferObjects;

use Domain\StaffMembers\Models\StaffMember;

class CreateUserData
{
     readonly int $role_id;

    public function __construct(
        readonly string $email,
        readonly string $name,
        readonly string $phone_number,
        readonly string $password
    ) {
        $this->role_id = StaffMember::where("email", $this->email)->first([
            "role_id",
        ])->role_id;
    }
}
