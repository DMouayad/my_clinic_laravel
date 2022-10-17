<?php

namespace Domain\StaffMembers\Exceptions;

use App\Exceptions\CustomException;
use Symfony\Component\HttpFoundation\Response;

class DeletingOnlyAdminStaffMemberException extends CustomException
{


    public function render($request)
    {
        return $this->errorResponseFromException(
            $this,
            "Cannot delete the only admin staff member, Create another staff member with admin role before deleting this one",
            Response::HTTP_CONFLICT
        );
    }
}
