<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\StaffMemberResource;
use App\Models\StaffMember;
use App\Services\StaffMemberService;
use App\Services\UserService;
use App\Traits\ProvidesApiJsonResponse;
use App\Traits\ProvidesResourcesJsonResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class StaffMemberController extends Controller
{
    use ProvidesResourcesJsonResponse, ProvidesApiJsonResponse;

    private StaffMemberService $staffMemberService;

    public function __construct(StaffMemberService $staffMemberService)
    {
        $this->middleware(["auth:sanctum", "ability:admin", "verified"]);
        $this->staffMemberService = $staffMemberService;
        $this->setResource(StaffMemberResource::class);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return JsonResource|null
     */
    public function getStaffMembersWithRoles(Request $request): ?JsonResource
    {
        return $this->getStaffMemberCollection($request, ["role"]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param array $relations
     * @return \Illuminate\Http\Resources\Json\JsonResource|null
     */
    private function getStaffMemberCollection(
        Request $request,
        array $relations = []
    ): ?JsonResource {
        $custom_sorting_fields = ["username", "role", "registered_with_at"];
        $query = StaffMember::with($relations);

        if ($request->sort) {
            $sorting_allowed_attributes = [
                "email",
                "created_at",
                ...$custom_sorting_fields,
            ];
            $sort_params = explode(",", $request->sort);
            $sort_fields = [];
            $sort_fields_direction = [];
            array_walk($sort_params, function ($item) use (
                &$sort_fields,
                &$sort_fields_direction,
                $sorting_allowed_attributes
            ) {
                $info = explode(" ", $item);
                if (in_array($info[0], $sorting_allowed_attributes)) {
                    $sort_fields[] = $info[0];
                    $sort_fields_direction[$info[0]] = strtolower($info[1]);
                }
            });

            // First, sort by StaffMember attributes
            /**
             * @var \Illuminate\Database\Eloquent\Builder $query
             */
            $query = $this->orderQueryFromUrlParam(
                $query,
                $request->sort,
                allowed: $sorting_allowed_attributes,
                ignored: $custom_sorting_fields
            );

            if (in_array("username", $sort_fields)) {
                $is_descending = $sort_fields_direction["username"] == "desc";

                $query->orderByRaw(
                    "(select name from users where staff_members.user_id = users.id) " .
                        ($is_descending ? "desc" : "is null")
                );
            }
            if (in_array("registered_with_at", $sort_fields)) {
                $is_descending =
                    $sort_fields_direction["registered_with_at"] == "desc";

                $query->orderByRaw(
                    ($is_descending ? "" : "-") .
                        "(select created_at from users where staff_members.user_id = users.id) desc"
                );
            }
            if (in_array("role", $sort_fields)) {
                $query->orderByRaw(
                    "(select slug from roles where staff_members.role_id = roles.id) " .
                        $sort_fields_direction["role"]
                );
            }
        }
        return $this->paginatedCollection(
            $query,
            per_page: $request->page ? null : 10000
        );
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return JsonResource|null
     */
    public function getStaffWithUsersAndRoles(Request $request): ?JsonResource
    {
        return $this->getStaffMemberCollection($request, ["role", "user"]);
    }

    /**
     * Get a list of the staff Emails without
     * loading their role relationship.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResource
     */
    public function getStaffMembers(Request $request): JsonResource
    {
        return $this->getStaffMemberCollection($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \App\Exceptions\CustomValidationException
     * @throws \App\Exceptions\FailedToSaveObjectException
     * @throws \App\Exceptions\RoleNotFoundException
     * @throws \App\Exceptions\StaffMemberAlreadyExistsException
     */
    public function store(Request $request)
    {
        $params = $this->customValidate($request, [
            "email" => "required|email",
            "role" => "required|string",
        ]);

        $newStaffMember = $this->staffMemberService->store(
            strtolower($params["email"]),
            strtolower($params["role"])
        );
        return $this->successResponse(
            new StaffMemberResource($newStaffMember->load("role")),
            status_code: Response::HTTP_CREATED
        );
    }

    /**
     * @param StaffMember $staff_member
     * @return JsonResource|null
     */
    public function show(StaffMember $staff_member)
    {
        return $this->resource($staff_member);
    }

    /**
     * @param Request $request
     * @param StaffMember $staff_member
     * @param UserService $userService
     * @return JsonResponse
     * @throws \App\Exceptions\CustomValidationException
     * @throws \App\Exceptions\EmailAlreadyRegisteredException
     * @throws \App\Exceptions\FailedToUpdateObjectException
     * @throws \App\Exceptions\PhoneNumberAlreadyUsedException
     * @throws \App\Exceptions\RoleNotFoundException
     * @throws \App\Exceptions\StaffMemberAlreadyExistsException
     * @throws \App\Exceptions\UserDoesntMatchHisStaffMemberException
     */
    public function update(
        Request $request,
        StaffMember $staff_member,
        UserService $userService
    ) {
        $validated = $this->customValidate($request, [
            "email" => "nullable|email",
            "role" => "nullable|string",
        ]);

        if (!empty($validated)) {
            // update staff_member with the provided data
            $updated_staff_member = $this->staffMemberService->update(
                $staff_member,
                strtolower(Arr::get($validated, "email")),
                strtolower(Arr::get($validated, "role"))
            );
            // then update staffMember's user if exists
            if ($updated_staff_member->user) {
                $userService->update(
                    user: $updated_staff_member->user,
                    role_id: $updated_staff_member->role_id,
                    email: $updated_staff_member->email
                );
            }
            return $this->successResponse(
                status_code: Response::HTTP_NO_CONTENT
            );
        } else {
            return $this->errorResponse(
                status_code: Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param StaffMember $staff_member
     * @param \App\Services\UserService $userService
     * @return JsonResponse
     * @throws \App\Exceptions\DeletingOnlyAdminStaffMemberException
     * @throws \App\Exceptions\FailedToDeleteObjectException
     * @throws \App\Exceptions\RoleNotFoundException
     * @throws \App\Exceptions\UnauthorizedToDeleteUserException
     */
    public function destroy(
        Request $request,
        StaffMember $staff_member,
        UserService $userService
    ): JsonResponse {
        if ($staff_member->user) {
            $userService->delete($staff_member->user, $request->user());
        }
        $this->staffMemberService->delete($staff_member);
        return $this->successResponse(status_code: Response::HTTP_NO_CONTENT);
    }
}
