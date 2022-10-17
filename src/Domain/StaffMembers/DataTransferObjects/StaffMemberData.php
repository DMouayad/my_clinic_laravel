<?php

namespace Domain\StaffMembers\DataTransferObjects;

class StaffMemberData
{
    private function __construct(
        readonly ?string $email,
        readonly ?string $role
    ) {
    }

    public static function forCreate(string $email, string $role): self
    {
        return new StaffMemberData($email, $role);
    }

    public static function forUpdate(
        ?string $email = null,
        ?string $role = null
    ): self {
        return new StaffMemberData($email, $role);
    }
}
