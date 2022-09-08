<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\EmailAlreadyRegisteredException;
use App\Exceptions\RoleNotFoundException;
use App\Exceptions\StaffEmailAlreadyExistsException;
use App\Exceptions\UserDoesntMatchHisStaffEmailException;
use App\Http\Controllers\Controller;
use App\Http\Resources\StaffEmailResource;
use App\Models\StaffEmail;
use App\Services\StaffEmailService;
use App\Services\UserService;
use App\Traits\PaginatesResources;
use App\Traits\ProvidesApiJsonResponse;
use App\Traits\ProvidesResourcesJsonResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

//use Illuminate\Support\Facades\Response;

class StaffEmailController extends Controller
{
    use ProvidesResourcesJsonResponse,
        PaginatesResources,
        ProvidesApiJsonResponse;

    private StaffEmailService $staffEmailService;

    public function __construct(StaffEmailService $staffEmailService)
    {
        $this->middleware(["auth:sanctum", "ability:admin", "verified"]);
        $this->staffEmailService = $staffEmailService;
        $this->setResource(StaffEmailResource::class);
    }

    /**
     * @return JsonResource|null
     */
    public function getEmailsWithRoles(): ?JsonResource
    {
        $staff_emails = $this->paginateWhenNeeded(StaffEmail::with("role"));

        return $this->collection($staff_emails);
    }

    /**
     * Get a list of the staff Emails without
     * loading their role relationship.
     * @return JsonResource
     */
    public function getEmailsOnly(): JsonResource
    {
        return $this->collection($this->paginateWhenNeeded(StaffEmail::all()));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws RoleNotFoundException
     * @throws StaffEmailAlreadyExistsException
     */
    public function store(Request $request)
    {
        $params = $request->validate([
            "email" => "required|email",
            "role" => "required|string",
        ]);

        $this->staffEmailService->store(
            strtolower($params["email"]),
            strtolower($params["role"])
        );
        return $this->successResponse(
            message: "Staff email created successfully.",
            status_code: Response::HTTP_CREATED
        );
    }

    /**
     * @param StaffEmail $staffEmail
     * @return JsonResource|null
     */
    public function show(StaffEmail $staffEmail)
    {
        return $this->resource($staffEmail);
    }

    /**
     * @param Request $request
     * @param StaffEmail $staffEmail
     * @param UserService $userService
     * @return JsonResponse|void
     * @throws EmailAlreadyRegisteredException
     * @throws RoleNotFoundException
     * @throws StaffEmailAlreadyExistsException
     * @throws UserDoesntMatchHisStaffEmailException
     */
    public function update(
        Request $request,
        StaffEmail $staffEmail,
        UserService $userService
    ) {
        $params = $request->validate([
            "email" => "nullable|email",
            "role" => "nullable|string",
        ]);

        if (!empty($params)) {
            $updated_staff_email = $this->staffEmailService->update(
                $staffEmail,
                strtolower(Arr::get($params, "email", default: null)),
                strtolower(Arr::get($params, "role", default: null))
            );
            $updated_user = $userService->update(
                user: $updated_staff_email->user,
                role_id: $updated_staff_email->role_id,
                email: $updated_staff_email->email
            );

            $user_was_updated = $updated_staff_email->user == $updated_user;
            if ($user_was_updated) {
                return $this->successResponse(
                    status_code: Response::HTTP_NO_CONTENT
                );
            }
        } else {
            return $this->errorResponse(
                status_code: Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @param StaffEmail $staffEmail
     * @return JsonResponse
     * @throws \App\Exceptions\DeletingOnlyAdminStaffEmailException
     */
    public function destroy(StaffEmail $staffEmail): JsonResponse
    {
        $was_deleted = $this->staffEmailService->delete($staffEmail);
        if ($was_deleted) {
            return $this->successResponse(message: "Deleted successfully");
        } else {
            return $this->errorResponse([
                "message" => "Failed to delete the Staff email",
            ]);
        }
    }
}
