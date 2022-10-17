<?php

namespace App\Api\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules()
    {
        return [
            "email" => "required|email",
            "password" => "required|string|min:8",
            "device_id" => "required|string",
        ];
    }
}
