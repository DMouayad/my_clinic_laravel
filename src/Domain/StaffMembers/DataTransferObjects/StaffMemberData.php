<?php

namespace Domain\StaffMembers\DataTransferObjects;

class StaffMemberData
{
     readonly ?string $email;

    private function __construct(
        ?string $email,
        readonly ?string $role,
        readonly ?int $user_id = null
    ) {
        $this->email = $email ? strtolower($email) : null;
    }

    public static function forCreate(string $email, string $role): self
    {
        return new StaffMemberData($email, $role);
    }

    public static function forUpdate(
        ?string $email = null,
        ?string $role = null,
        ?int $user_id = null
    ): self {
        return new StaffMemberData($email, $role, $user_id);
    }
}
