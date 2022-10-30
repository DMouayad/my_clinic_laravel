<?php

namespace App\Api\Admin\StaffMembers\Controllers;

use App\Api\Admin\StaffMembers\Queries\StaffMembersIndexQuery;
use App\Api\Admin\StaffMembers\Requests\AddStaffMemberRequest;
use App\Api\Admin\StaffMembers\Requests\UpdateStaffMemberRequest;
use App\Api\Admin\StaffMembers\Resources\StaffMemberResource;
use App\Exceptions\EmailAlreadyRegisteredException;
use App\Exceptions\FailedToDeleteObjectException;
use App\Exceptions\FailedToSaveObjectException;
use App\Exceptions\FailedToUpdateObjectException;
use App\Exceptions\UnauthorizedToDeleteUserException;
use App\Http\Controllers\Controller;
use Domain\StaffMembers\Actions\AddStaffMemberAction;
use Domain\StaffMembers\Actions\DeleteStaffMemberAction;
use Domain\StaffMembers\Actions\UpdateStaffMemberAction;
use Domain\StaffMembers\DataTransferObjects\StaffMemberData;
use Domain\StaffMembers\Exceptions\DeletingOnlyAdminStaffMemberException;
use Domain\StaffMembers\Exceptions\StaffMemberAlreadyExistsException;
use Domain\StaffMembers\Models\StaffMember;
use Domain\Users\Exceptions\RoleNotFoundException;
use Domain\Users\Exceptions\UserDoesntMatchHisStaffMemberException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Support\Traits\ProvidesApiJsonResponse;
use Support\Traits\ProvidesResourcesJsonResponse;
use Symfony\Component\HttpFoundation\Response;

class StaffMemberController extends Controller
{
    use ProvidesResourcesJsonResponse, ProvidesApiJsonResponse;

    public function __construct()
    {
        $this->setResource(StaffMemberResource::class);
    }

    /**
     * @throws RoleNotFoundException
     * @throws FailedToDeleteObjectException
     * @throws UnauthorizedToDeleteUserException
     * @throws DeletingOnlyAdminStaffMemberException
     */
    public function destroy(
        Request $request,
        StaffMember $staff_member,
        DeleteStaffMemberAction $action
    ): JsonResponse {
        $action->execute(
            staff_member: $staff_member,
            request_user: $request->user()
        );

        return $this->successResponse(status_code: Response::HTTP_NO_CONTENT);
    }

    /**
     * Get a list of the staff Emails without
     * loading their role relationship.
     */
    public function index(
        StaffMembersIndexQuery $query,
        Request $request
    ): JsonResource {
        return $this->paginatedResourceCollection(
            $query,
            per_page: $request->get("per_page")
        );
    }

    /**
     * @param  StaffMembersIndexQuery  $query
     * @param  Request  $request
     *
     * @return JsonResource|null
     */
    public function indexWithRoles(
        StaffMembersIndexQuery $query,
        Request $request
    ): ?JsonResource {
        return $this->paginatedResourceCollection(
            $query->with(["role:id,slug"]),
            per_page: $request->get("per_page")
        );
    }

    public function indexWithRolesAndUsers(
        StaffMembersIndexQuery $query,
        Request $request
    ): ?JsonResource {
        return $this->paginatedResourceCollection(
            $query->with(["user", "role:id,slug"]),
            per_page: $request->get("per_page")
        );
    }

    /**
     * @throws FailedToSaveObjectException
     * @throws RoleNotFoundException
     * @throws StaffMemberAlreadyExistsException
     */
    public function store(
        AddStaffMemberRequest $request,
        AddStaffMemberAction $action
    ): JsonResponse {
        $params = $request->validated();
        $newStaffMember = $action->execute(
            StaffMemberData::forCreate(...$params)
        );
        return $this->successResponse(
            new StaffMemberResource($newStaffMember->load("role")),
            status_code: Response::HTTP_CREATED
        );
    }

    /**
     * @throws FailedToUpdateObjectException
     * @throws RoleNotFoundException
     * @throws StaffMemberAlreadyExistsException
     * @throws UserDoesntMatchHisStaffMemberException
     * @throws EmailAlreadyRegisteredException
     */
    public function update(
        UpdateStaffMemberRequest $request,
        StaffMember $staff_member,
        UpdateStaffMemberAction $updateStaffMemberAction
    ): JsonResponse {
        $validated = $request->validated();
        $updateStaffMemberAction->execute(
            $staff_member,
            StaffMemberData::forUpdate(...$validated)
        );
        return $this->successResponse(status_code: Response::HTTP_NO_CONTENT);
    }
}
