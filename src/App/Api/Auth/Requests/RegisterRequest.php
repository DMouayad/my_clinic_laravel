<?php

namespace App\Api\Auth\Requests;

use App\Api\Users\Requests\CreateUserRequest;

class RegisterRequest extends CreateUserRequest
{
    public function rules()
    {
        return parent::rules() + [
            "device_id" => "required|string",
        ];
    }
}
