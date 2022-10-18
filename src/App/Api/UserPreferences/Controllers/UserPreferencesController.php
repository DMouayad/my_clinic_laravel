<?php

namespace App\Api\UserPreferences\Controllers;

use App\Api\UserPreferences\Requests\CreateUserPreferencesRequest;
use App\Api\UserPreferences\Requests\UpdateUserPreferencesRequest;
use App\Api\UserPreferences\Resources\UserPreferencesResource;
use App\Http\Controllers\Controller;
use Domain\UserPreferences\Actions\CreateUserPreferencesAction;
use Domain\UserPreferences\Actions\DeleteUserPreferencesAction;
use Domain\UserPreferences\Actions\UpdateUserPreferencesAction;
use Domain\UserPreferences\DataTransferObjects\UserPreferencesData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
     * @throws \App\Exceptions\FailedToSaveObjectException
     */
    public function store(
        CreateUserPreferencesRequest $request,
        CreateUserPreferencesAction $action
    ): JsonResponse {
        $validated = $request->validated();
        $action->execute(
            UserPreferencesData::forCreate(
                theme: $validated["theme"],
                locale: $validated["locale"],
                user_id: $request->user()->id
            )
        );
        return $this->successResponse(status_code: Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource|null
     */
    public function show(Request $request)
    {
        return $this->resource($request->user()->preferences);
    }

    /**
     * @throws \App\Exceptions\FailedToSaveObjectException
     * @throws \App\Exceptions\UpdateRequestForNonExistingObjectException
     */
    public function update(
        UpdateUserPreferencesRequest $request,
        UpdateUserPreferencesAction $action
    ): JsonResponse {
        $validated = $request->validated();
        $action->execute(
            user_preferences: $request->user()->preferences,
            data: UserPreferencesData::forUpdate(
                theme: Arr::get($validated, "theme"),
                locale: Arr::get($validated, "locale"),
                user_id: $request->user()->id
            )
        );
        return $this->successResponse(status_code: Response::HTTP_NO_CONTENT);
    }

    /**
     * @throws \App\Exceptions\FailedToDeleteObjectException
     * @throws \App\Exceptions\DeleteAttemptOfNonExistingModelException
     */
    public function destroy(
        Request $request,
        DeleteUserPreferencesAction $action
    ): JsonResponse {
        $action->execute($request->user()->preferences);
        return $this->successResponse(status_code: Response::HTTP_NO_CONTENT);
    }
}
