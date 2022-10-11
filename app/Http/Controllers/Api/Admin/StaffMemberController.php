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
        return $this->collectionOfRequestQuery($request, StaffMember::query(), [
            "role",
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return JsonResource|null
     */
    public function getStaffWithUsersAndRoles(Request $request): ?JsonResource
    {
        return $this->collectionOfRequestQuery($request, StaffMember::query(), [
            "role",
            "user",
        ]);
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
        return $this->collectionOfRequestQuery($request, StaffMember::query());
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
        Request     $request,
        StaffMember $staff_member,
        UserService $userService
    )
    {
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
        Request     $request,
        StaffMember $staff_member,
        UserService $userService
    ): JsonResponse
    {
        if ($staff_member->user) {
            $userService->delete($staff_member->user, $request->user());
        }
        $this->staffMemberService->delete($staff_member);
        return $this->successResponse(status_code: Response::HTTP_NO_CONTENT);
    }
}
