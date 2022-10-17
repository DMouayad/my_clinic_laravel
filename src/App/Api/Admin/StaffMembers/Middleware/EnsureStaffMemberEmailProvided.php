<?php

namespace App\Api\Admin\StaffMembers\Middleware;

use App\Exceptions\EmailUnauthorizedToRegisterException;
use Closure;
use Domain\StaffMembers\Models\StaffMember;
use Illuminate\Http\Request;
use Support\Traits\ProvidesApiJsonResponse;
use Support\Traits\ProvidesCustomRequestValidator;

class EnsureStaffMemberEmailProvided
{
    use ProvidesApiJsonResponse, ProvidesCustomRequestValidator;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $email_to_register = $this->customValidate($request, [
            "email" => "required|email",
        ])["email"];

        $staff_member = StaffMember::where(
            "email",
            $email_to_register
        )->first();

        if ($staff_member) {
            return $next($request);
        } else {
            throw new EmailUnauthorizedToRegisterException($email_to_register);
        }
    }
}
