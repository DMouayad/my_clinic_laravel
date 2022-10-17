<?php

namespace App\Api\Users\Requests;

use App\Exceptions\CustomValidationException;
use App\Exceptions\EmailAlreadyRegisteredException;
use App\Exceptions\PhoneNumberAlreadyUsedException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Support\Helpers\ValidatorHelper;

abstract class BaseUserRequest extends FormRequest
{
    public function rules()
    {
        return [
            "name" => $this->getNameRule(),
            "email" => $this->getEmailRule(),
            "phone_number" => $this->getPhoneNoRule(),
        ];
    }

    abstract public function getNameRule(): array|string;

    abstract public function getEmailRule(): array|string;

    abstract public function getPhoneNoRule(): array|string;

    protected function failedValidation(Validator $validator)
    {
        if (ValidatorHelper::notUnique($validator, "email")) {
            throw new EmailAlreadyRegisteredException(
                $this->request->get("email")
            );
        }
        if (ValidatorHelper::notUnique($validator, "phone_number")) {
            throw new PhoneNumberAlreadyUsedException(
                $this->request->get("phone_number")
            );
        }
        throw new CustomValidationException($validator);
    }
}
