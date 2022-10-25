<?php

namespace Domain\Users\DataTransferObjects;
class CreateUserData
{
     readonly string $email;

    public function __construct(
        string $email,
        readonly string $name,
        readonly string $phone_number,
        readonly string $password,
        readonly int $role_id
    ) {
        $this->email = $email ? strtolower($email) : null;
    }
}
