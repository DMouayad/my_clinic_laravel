<?php

namespace Domain\Users\DataTransferObjects;

class UpdateUserData
{
    private function __construct(
        readonly ?string $name = null,
        readonly ?string $email = null,
        readonly ?string $phone_number = null,
        readonly ?int $role_id = null
    ) {
    }

    public static function fromStaffMember(string $email, int $role_id): self
    {
        return new self(email: $email, role_id: $role_id);
    }

    public static function new(
        ?string $name = null,
        ?string $email = null,
        ?string $phone_number = null,
        ?int $role_id = null
    ): self {
        return new self(
            name: $name,
            email: $email,
            phone_number: $phone_number,
            role_id: $role_id
        );
    }
}
