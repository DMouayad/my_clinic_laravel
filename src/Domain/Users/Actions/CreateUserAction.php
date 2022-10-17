<?php

namespace Domain\Users\Actions;

use App\Models\User;
use Domain\Users\DataTransferObjects\CreateUserData;
use Illuminate\Support\Facades\Hash;

class CreateUserAction
{
    public function execute(CreateUserData $data): User
    {
        return User::create([
            "email" => $data->email,
            "name" => $data->name,
            "phone_number" => $data->phone_number,
            "password" => Hash::make($data->password),
            "role_id" => $data->role_id,
        ]);
    }
}
