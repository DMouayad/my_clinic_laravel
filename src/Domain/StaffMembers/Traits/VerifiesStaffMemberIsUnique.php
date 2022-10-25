<?php

namespace Domain\StaffMembers\Traits;

use Domain\StaffMembers\Exceptions\StaffMemberAlreadyExistsException;
use Domain\StaffMembers\Models\StaffMember;

trait VerifiesStaffMemberIsUnique
{
    private function verifyEmailIsUnique(string $email, ?string $exclude = null)
    {
        if (
            StaffMember::query()
                ->where("email", $email)
                ->whereNot("email", $exclude)
                ->exists()
        ) {
            throw new StaffMemberAlreadyExistsException();
        }
    }
}
