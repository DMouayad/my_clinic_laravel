<?php

namespace App\Http\Middleware;

use App\Exceptions\EmailUnauthorizedToRegisterException;
use App\Models\StaffEmail;
use App\Traits\ProvidesApiJsonResponse;
use App\Traits\ProvidesCustomRequestValidator;
use Closure;
use Illuminate\Http\Request;

class EnsureStaffEmailProvided
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

        $staff_email = StaffEmail::where("email", $email_to_register)->first();

        if ($staff_email) {
            return $next($request);
        } else {
            throw new EmailUnauthorizedToRegisterException($email_to_register);
        }
    }
}
