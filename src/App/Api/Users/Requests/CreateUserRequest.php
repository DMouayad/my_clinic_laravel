<?php

namespace App\Api\Users\Requests;

class CreateUserRequest extends BaseUserRequest
{
    public function rules()
    {
        return parent::rules() + [
            "password" => "required|string|min:8",
        ];
    }

    public function getNameRule(): array|string
    {
        return "required|string";
    }

    public function getEmailRule(): array|string
    {
        return ["required", "email", "unique:users"];
    }

    public function getPhoneNoRule(): array|string
    {
        return ["required", "string", "unique:users"];
    }
}
