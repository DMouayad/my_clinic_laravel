<?php

namespace App\Api\Admin\StaffMembers\Requests;

use App\Exceptions\CustomValidationException;
use Domain\StaffMembers\Exceptions\StaffMemberAlreadyExistsException;
use Domain\Users\Exceptions\RoleNotFoundException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Support\Helpers\ValidatorHelper;

abstract class BaseStaffMemberRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function rules()
    {
        return [
            "email" => $this->emailRules(),
            "role" => $this->roleRules(),
        ];
    }

    abstract function emailRules(): array;

    abstract function roleRules(): array;

    protected function failedValidation(Validator $validator)
    {
        if (ValidatorHelper::notUnique($validator, "email")) {
            throw new StaffMemberAlreadyExistsException();
        }
        if (ValidatorHelper::notInAllowedElements($validator, "role")) {
            throw new RoleNotFoundException($this->request->get("role"));
        }
        throw new CustomValidationException($validator);
    }
}
