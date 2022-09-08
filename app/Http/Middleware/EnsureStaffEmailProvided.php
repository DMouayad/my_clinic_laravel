<?php

namespace App\Http\Middleware;

use App\Models\StaffEmail;
use App\Traits\ProvidesApiJsonResponse;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnsureStaffEmailProvided
{
    use ProvidesApiJsonResponse;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $email_to_register = $request->validate(['email' => 'required|email'])['email'];

        $staff_email = StaffEmail::where('email', $email_to_register)->first();

        if ($staff_email) {
            return $next($request);
        } else {
            return $this->errorResponse(
                [
                    'message' => 'The email address (' . $email_to_register . ') is not allowed to register.',
                ],
                JsonResponse::HTTP_FORBIDDEN,
            );
        }
    }
}
