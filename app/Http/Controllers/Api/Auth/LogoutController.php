<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ProvidesApiJsonResponse;
use Symfony\Component\HttpFoundation\Response;

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
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(status_code: Response::HTTP_NO_CONTENT);
    }
}
