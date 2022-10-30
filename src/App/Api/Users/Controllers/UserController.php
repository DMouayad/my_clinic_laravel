<?php

namespace App\Api\Users\Controllers;

use App\Api\Users\Requests\UpdateUserRequest;
use App\Api\Users\Resources\UserResource;
use App\Exceptions\FailedToDeleteObjectException;
use App\Exceptions\UnauthorizedToDeleteUserException;
use App\Http\Controllers\Controller;
use Domain\Users\Actions\DeleteUserAction;
use Domain\Users\Actions\UpdateUserAction;
use Domain\Users\DataTransferObjects\UpdateUserData;
use Domain\Users\Events\UserWasDeleted;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Support\Traits\ProvidesApiJsonResponse;
use Support\Traits\ProvidesResourcesJsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    use ProvidesResourcesJsonResponse, ProvidesApiJsonResponse;

    public function __construct()
    {
        $this->setResource(UserResource::class);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws FailedToDeleteObjectException
     * @throws UnauthorizedToDeleteUserException
     */
    public function deleteCurrentUser(
        Request $request,
        DeleteUserAction $action
    ): JsonResponse {
        $action->execute($request->user(), $request->user());
        event(new UserWasDeleted($request->user()));
        return $this->successResponse(status_code: Response::HTTP_NO_CONTENT);
    }

    /**
     * @param  Request  $request
     *
     * @return JsonResource|null
     */
    public function show(Request $request): ?JsonResource
    {
        return $this->resource(
            $request
                ->user()
                ->load([
                    "preferences:id,user_id,theme,locale",
                    "role:id,name,slug",
                ])
        );
    }

    public function update(UpdateUserRequest $request, UpdateUserAction $action)
    {
        $validated = $request->validated();
        $action->execute($request->user(), new UpdateUserData(...$validated));
        return $this->successResponse(status_code: Response::HTTP_NO_CONTENT);
    }
}
