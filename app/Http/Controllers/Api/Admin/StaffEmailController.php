<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\StaffEmailResource;
use App\Models\StaffEmail;
use App\Services\StaffEmailService;
use App\Services\UserService;
use App\Traits\ProvidesApiJsonResponse;
use App\Traits\ProvidesResourcesJsonResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class StaffEmailController extends Controller
{
    use ProvidesResourcesJsonResponse, ProvidesApiJsonResponse;

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
        return $this->paginatedCollection(StaffEmail::with("role"));
    }

    /**
     * @return JsonResource|null
     */
    public function getEmailsWithUsersAndRoles(): ?JsonResource
    {
        return $this->paginatedCollection(StaffEmail::with(["role", "user"]));
    }

    /**
     * Get a list of the staff Emails without
     * loading their role relationship.
     * @return JsonResource
     */
    public function getEmailsOnly(): JsonResource
    {
        return $this->paginatedCollection(StaffEmail::paginate());
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \App\Exceptions\CustomValidationException
     * @throws \App\Exceptions\FailedToSaveObjectException
     * @throws \App\Exceptions\RoleNotFoundException
     * @throws \App\Exceptions\StaffEmailAlreadyExistsException
     */
    public function store(Request $request)
    {
        $params = $this->customValidate($request, [
            "email" => "required|email",
            "role" => "required|string",
        ]);

        $newStaffEmail = $this->staffEmailService->store(
            strtolower($params["email"]),
            strtolower($params["role"])
        );
        return $this->successResponse(
            new StaffEmailResource($newStaffEmail->load('role')),
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
     * @return JsonResponse
     * @throws \App\Exceptions\CustomValidationException
     * @throws \App\Exceptions\EmailAlreadyRegisteredException
     * @throws \App\Exceptions\FailedToUpdateObjectException
     * @throws \App\Exceptions\PhoneNumberAlreadyUsedException
     * @throws \App\Exceptions\RoleNotFoundException
     * @throws \App\Exceptions\StaffEmailAlreadyExistsException
     * @throws \App\Exceptions\UserDoesntMatchHisStaffEmailException
     */
    public function update(
        Request $request,
        StaffEmail $staffEmail,
        UserService $userService
    ) {
        $params = $this->customValidate($request, [
            "email" => "nullable|email",
            "role" => "nullable|string",
        ]);

        if (!empty($params)) {
            // update staffEmail with the provided data
            $updated_staff_email = $this->staffEmailService->update(
                $staffEmail,
                strtolower(Arr::get($params, "email")),
                strtolower(Arr::get($params, "role"))
            );
            // then update staffEmail's user if exists
            if ($updated_staff_email->user) {
                $userService->update(
                    user: $updated_staff_email->user,
                    role_id: $updated_staff_email->role_id,
                    email: $updated_staff_email->email
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
     * @param StaffEmail $staffEmail
     * @return JsonResponse
     * @throws \App\Exceptions\DeletingOnlyAdminStaffEmailException
     * @throws \App\Exceptions\FailedToDeleteObjectException
     * @throws \App\Exceptions\RoleNotFoundException
     */
    public function destroy(StaffEmail $staffEmail): JsonResponse
    {
        $this->staffEmailService->delete($staffEmail);
        return $this->successResponse();
    }
}
