<?php

namespace App\Api\Admin\StaffMembers\Requests;

use Illuminate\Validation\Rule;

class AddStaffMemberRequest extends BaseStaffMemberRequest
{
    function emailRules(): array
    {
        return ["required", "email", "unique:staff_members"];
    }

    function roleRules(): array
    {
        return [
            "required",
            Rule::in(["admin", "dentist", "secretary", "patient"]),
        ];
    }
}
