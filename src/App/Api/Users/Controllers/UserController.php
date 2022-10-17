<?php

namespace App\Api\Users\Controllers;

use App\Api\Users\Requests\UpdateUserRequest;
use App\Api\Users\Resources\UserResource;
use App\Http\Controllers\Controller;
use Domain\Users\Actions\DeleteUserAction;
use Domain\Users\Actions\UpdateUserAction;
use Domain\Users\DataTransferObjects\UpdateUserData;
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
     * @throws \App\Exceptions\FailedToDeleteObjectException
     * @throws \App\Exceptions\UnauthorizedToDeleteUserException
     */
    public function destroy(Request $request, DeleteUserAction $action)
    {
        $action->execute($request->user(), $request->user());
        return $this->successResponse(status_code: Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
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
        $action->execute($request->user(), UpdateUserData::new(...$validated));
        return $this->successResponse(status_code: Response::HTTP_NO_CONTENT);
    }
}
