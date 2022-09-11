<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use App\Traits\PaginatesResources;
use App\Traits\ProvidesApiJsonResponse;
use App\Traits\ProvidesResourcesJsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    use ProvidesResourcesJsonResponse,
        PaginatesResources,
        ProvidesApiJsonResponse;

    public function __construct()
    {
        $this->middleware(["auth:sanctum", "verified"])->only('show');
        $this->middleware(["auth:sanctum", 'ability:admin', "verified"])->except('show');
        $this->setResource(UserResource::class);
        $this->setPerPage(env("USER_PER_PAGE"));
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResource|null
     **/
    public function index(): ?JsonResource
    {
        return $this->collection(
            $this->paginateWhenNeeded(User::with('role')->get())
        );
    }

    /**
     * Get staff users only.
     *
     * @return JsonResource|null
     **/
    public function getStaffUsers(): ?JsonResource
    {
        $patient_role_id = Role::getIdBySlug("patient");
        return $this->collection(
            $this->paginateWhenNeeded(
                User::whereNot("role_id", $patient_role_id)->with('role')
            )
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(
        Request $request,
        User $user,
        UserService $userService
    ) {
        $deleted = $userService->delete($user, $request->user());
        if ($deleted) {
            return $this->successResponse(
                status_code: Response::HTTP_NO_CONTENT
            );
        } else {
            return $this->errorResponse([
                "message" =>
                "user with id (" . $user->id . ") was not deleted!",
            ]);
        }
    }

    /**
     * @param Request $request
     * @return JsonResource|null
     */
    public function show(Request $request): ?JsonResource
    {
        return $this->resource($request->user()->load('preferences'));
    }
}
