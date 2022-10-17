<?php

namespace App\Api\Users\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends BaseUserRequest
{
    public function getNameRule(): array|string
    {
        return "required_without_all:email,phone_number";
    }

    public function getEmailRule(): array|string
    {
        return [
            "required_without_all:name,phone_number",
            "email",
            "unique:users",
        ];
    }

    public function getPhoneNoRule(): array|string
    {
        return [
            "required_without_all:email,name",
            "string",
            Rule::unique("users")->whereNot("id", Auth::id()),
        ];
    }
}
