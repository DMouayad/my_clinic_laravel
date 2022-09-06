<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\StaffEmail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\StaffEmailResource;
use App\Services\StaffEmailService;
use App\Services\UserService;
use App\Traits\ProvidesResourcesJsonResponse;
use App\Traits\PaginatesResources;
use Illuminate\Support\Arr;
use App\Traits\ProvidesApiJsonResponse;
use Illuminate\Http\JsonResponse;

class StaffEmailController extends Controller
{
    use ProvidesResourcesJsonResponse, PaginatesResources, ProvidesApiJsonResponse;

    private StaffEmailService $staffEmailService;

    public function __construct(StaffEmailService $staffEmailService)
    {
        $this->middleware(['auth:sanctum', 'ability:admin', 'verified']);
        $this->staffEmailService = $staffEmailService;
        $this->setResource(StaffEmailResource::class);
    }

    /**
     * Get Staff emails with their role slug.
     *
     * @return \App\Http\Resources\StaffEmailResource
     */
    public function getEmailsWithRoles()
    {
        $staff_emails = $this->paginateWhenNeeded(StaffEmail::with('role'));
        return $this->collection($staff_emails);
    }

    /**
     * Get a list of the staff Emails without
     * loading their role relationship. 
     * @return \App\Http\Resources\StaffEmailResource
     */
    public function getEmailsOnly()
    {
        return  $this->collection($this->paginateWhenNeeded(StaffEmail::all()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $params = $request->validate(['email' => 'required|email', 'role' => 'required|string']);

        $this->staffEmailService->store(
            strtolower($params['email']),
            strtolower($params['role'])
        );
        return $this->successResponse(message: 'Staff email created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StaffEmail  $staffEmail
     * @return \App\Http\Resources\StaffEmailResource
     */
    public function show(StaffEmail $staffEmail)
    {
        return $this->resource($staffEmail);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StaffEmail  $staffEmail
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, StaffEmail $staffEmail, UserService $userService)
    {
        $params = $request->validate([
            'email' => 'nullable|email',
            'role' => 'nullable|string',
        ]);

        if (!empty($params)) {
            $updated_staff_email =  $this->staffEmailService->update(
                $staffEmail,
                strtolower(Arr::get($params, 'email', default: null)),
                strtolower(Arr::get($params, 'role', default: null)),
            );
            $updated_user = $userService->update(
                $updated_staff_email->user,
                $updated_staff_email->email,
                $updated_staff_email->role_id
            );

            $user_was_updated = $updated_staff_email->user ==  $updated_user;
            if ($user_was_updated) {
                return $this->successResponse();
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StaffEmail  $staffEmail
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(StaffEmail $staffEmail): JsonResponse
    {
        $was_deleted = $this->staffEmailService->delete($staffEmail);
        if ($was_deleted) {
            return $this->successResponse(message: 'Deleted successfully');
        } else {
            return $this->errorResponse(['message' => 'Failed to delete the Staff email']);
        }
    }
}
