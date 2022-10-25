<?php

namespace App\Api\UserPreferences\Controllers;

use App\Api\UserPreferences\Requests\AddUserPreferencesRequest;
use App\Api\UserPreferences\Requests\UpdateUserPreferencesRequest;
use App\Api\UserPreferences\Resources\UserPreferencesResource;
use App\Exceptions\DeleteAttemptOfNonExistingModelException;
use App\Exceptions\FailedToDeleteObjectException;
use App\Exceptions\FailedToSaveObjectException;
use App\Exceptions\UpdateRequestForNonExistingObjectException;
use App\Http\Controllers\Controller;
use Domain\UserPreferences\Actions\CreateUserPreferencesAction;
use Domain\UserPreferences\Actions\DeleteUserPreferencesAction;
use Domain\UserPreferences\Actions\UpdateUserPreferencesAction;
use Domain\UserPreferences\DataTransferObjects\UserPreferencesData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Support\Traits\ProvidesApiJsonResponse;
use Support\Traits\ProvidesResourcesJsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserPreferencesController extends Controller
{
    use ProvidesApiJsonResponse, ProvidesResourcesJsonResponse;

    public function __construct()
    {
        $this->setResource(UserPreferencesResource::class);
    }

    /**
     * @throws FailedToSaveObjectException
     * @throws UpdateRequestForNonExistingObjectException
     * @throws \Domain\Users\Exceptions\UserNotFoundException
     */
    public function store(
        AddUserPreferencesRequest $request,
        CreateUserPreferencesAction $action
    ): JsonResponse {
        $validated = $request->validated();
        $action->execute(
            UserPreferencesData::forCreate(
                user_id: $request->user()->id,
                theme: $validated["theme"],
                locale: $validated["locale"]
            )
        );
        return $this->successResponse(status_code: Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @return JsonResource|null
     */
    public function show(Request $request)
    {
        return $this->resource($request->user()->preferences);
    }

    /**
     * @throws FailedToSaveObjectException
     * @throws UpdateRequestForNonExistingObjectException
     */
    public function update(
        UpdateUserPreferencesRequest $request,
        UpdateUserPreferencesAction $action
    ): JsonResponse {
        $validated = $request->validated();
        $action->execute(
            user_preferences: $request->user()->preferences,
            data: UserPreferencesData::forUpdate(
                user_id: $request->user()->id,
                theme: Arr::get($validated, "theme"),
                locale: Arr::get($validated, "locale")
            )
        );
        return $this->successResponse(status_code: Response::HTTP_NO_CONTENT);
    }

    /**
     * @throws FailedToDeleteObjectException
     * @throws DeleteAttemptOfNonExistingModelException
     */
    public function destroy(
        Request $request,
        DeleteUserPreferencesAction $action
    ): JsonResponse {
        $action->execute($request->user()->preferences);
        return $this->successResponse(status_code: Response::HTTP_NO_CONTENT);
    }
}
