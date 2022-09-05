<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ProvidesApiJsonResponse;

class LogoutController extends Controller
{
    use ProvidesApiJsonResponse;

    public function __construct()
    {
        $this->middleware(['auth:sanctum']);
    }
    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(message: 'You have been logged out successfully');
    }
}
