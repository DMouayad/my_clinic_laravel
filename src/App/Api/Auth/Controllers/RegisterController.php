<?php

namespace App\Api\Auth\Controllers;

use App\Api\Auth\Requests\RegisterRequest;
use App\Exceptions\EmailAlreadyRegisteredException;
use App\Exceptions\FailedToUpdateObjectException;
use App\Http\Controllers\Controller;
use Domain\StaffMembers\Exceptions\StaffMemberAlreadyExistsException;
use Domain\StaffMembers\Models\StaffMember;
use Domain\Users\Actions\CreateUserAction;
use Domain\Users\DataTransferObjects\CreateUserData;
use Domain\Users\Exceptions\RoleNotFoundException;
use Domain\Users\Exceptions\UserDoesntMatchHisStaffMemberException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Support\Traits\ProvidesApiJsonResponse;
use Support\Traits\ProvidesResponseTokens;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    use ProvidesApiJsonResponse, ProvidesResponseTokens;

    /**
     * @throws EmailAlreadyRegisteredException
     * @throws FailedToUpdateObjectException
     * @throws StaffMemberAlreadyExistsException
     * @throws RoleNotFoundException
     * @throws UserDoesntMatchHisStaffMemberException
     */
    public function register(
        RegisterRequest $request,
        CreateUserAction $action
    ): JsonResponse {
        $data = $request
            ->safe()
            ->merge([
                "role_id" => StaffMember::findWhereEmail(
                    $request->safe()["email"]
                )->role->id,
            ])
            ->except(["device_id"]);
        $user = $action->execute(new CreateUserData(...$data));

        // dispatch a registered event to send a verification email to the user.
        event(new Registered($user));

        $role_slug = $user->role->slug;
        $device_id = $request->safe()["device_id"];

        $tokens = $this->getResponseTokens(
            $user->createRefreshToken($device_id),
            $user->createToken($device_id, [$role_slug])
        );
        return $this->successResponse(
            ["user" => $user, ...$tokens],
            message: "user was created successfully",
            status_code: Response::HTTP_CREATED
        );
    }
}
