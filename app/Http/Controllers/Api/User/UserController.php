<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\CustomError;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use App\Traits\ProvidesApiJsonResponse;
use App\Traits\ProvidesResourcesJsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    use ProvidesResourcesJsonResponse, ProvidesApiJsonResponse;

    public function __construct()
    {
        $this->middleware(["auth:sanctum", "verified"])->only("show");
        $this->middleware([
            "auth:sanctum",
            "ability:admin",
            "verified",
        ])->except("show");

        $this->setResource(UserResource::class);
        $this->setPerPageCount(env("USER_PER_PAGE"));
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResource|null
     **/
    public function index(): ?JsonResource
    {
        return $this->paginatedCollection(User::with("role"));
    }

    /**
     * Get staff users only.
     *
     * @return JsonResource|null
     **/
    public function getStaffUsers(): ?JsonResource
    {
        $patient_role_id = Role::getIdBySlug("patient");
        return $this->paginatedCollection(
            User::whereNot("role_id", $patient_role_id)->with("role")
        );
    }

    public function deleteMyAccount(Request $request, UserService $userService)
    {
        return $this->destroy($request, $request->user(), $userService);
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
            return $this->errorResponse(
                error: new CustomError(
                    "user with id (" . $user->id . ") was not deleted!"
                )
            );
        }
    }

    /**
     * @param Request $request
     * @return JsonResource|null
     */
    public function show(Request $request): ?JsonResource
    {
        return $this->resource($request->user()->load(["preferences", "role"]));
    }
}
