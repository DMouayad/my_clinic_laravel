<?php

namespace App\Api\Admin\StaffMembers\Requests;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateStaffMemberRequest extends BaseStaffMemberRequest
{
    function emailRules(): array
    {
        $updated_model_id = intval(Str::reverse($this->getRequestUri())[0]);
        return [
            "required_without:role",
            "email",
            Rule::unique("staff_members", "email")->whereNot(
                "id",
                $updated_model_id
            ),
        ];
    }

    function roleRules(): array
    {
        return [
            "required_without:email",
            Rule::in(["admin", "dentist", "secretary", "patient"]),
        ];
    }
}
