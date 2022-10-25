<?php

namespace Domain\Users\DataTransferObjects;

class UpdateUserData
{
     readonly ?string $email;

    public function __construct(
        readonly ?string $name = null,
        ?string $email = null,
        readonly ?string $phone_number = null,
        readonly ?int $role_id = null
    ) {
        $this->email = $email ? strtolower($email) : null;
    }
}
