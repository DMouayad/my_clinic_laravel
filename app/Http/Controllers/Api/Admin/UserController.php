<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Traits\ProvidesResourcesJsonResponse;
use App\Traits\PaginatesResources;
use \Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ProvidesApiJsonResponse;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    use ProvidesResourcesJsonResponse, PaginatesResources, ProvidesApiJsonResponse;

    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'verified']);
        $this->setResource(UserResource::class);
        $this->setPerPage(env('USER_PER_PAGE'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return JsonResource|null
     **/
    public function index()
    {
        return $this->collection($this->paginateWhenNeeded(User::all()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, User $user, UserService $userService)
    {
        $deleted =  $userService->deleteUser($user, $request->user());
        if ($deleted) {
            return $this->successResponse(status_code: JsonResponse::HTTP_NO_CONTENT);
        } else {
            return $this->errorResponse(
                ['message' => 'user with id (' . $user->id . ') was not deleted!']
            );
        }
    }

    /**
     * Return Auth user
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function me(Request $request)
    {
        return $this->resource($request->user());
    }
}
