<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserPreferencesResource;
use App\Services\UserPreferencesService;
use App\Traits\ProvidesApiJsonResponse;
use App\Traits\ProvidesResourcesJsonResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class UserPreferencesController extends Controller
{
    use ProvidesApiJsonResponse, ProvidesResourcesJsonResponse;

    public function __construct(
        private readonly UserPreferencesService $userPreferencesService
    )
    {
        $this->middleware(["auth:sanctum", "verified"]);
        $this->setResource(UserPreferencesResource::class);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\CustomValidationException
     * @throws \App\Exceptions\FailedToSaveObjectException
     * @throws \App\Exceptions\UserNotFoundException
     * @throws \App\Exceptions\UserPreferencesAlreadyExistsException
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $this->customValidate(
            $request,
            $this->getValidationRules(false)
        );
        $this->userPreferencesService->store(
            $request->user()->id,
            Arr::get($validated, "theme", "system"),
            Arr::get($validated, "locale", "en")
        );
        return $this->successResponse(status_code: Response::HTTP_CREATED);
    }

    /**
     * @param bool $isUpdateRequest
     * @return array|array[]
     */
    private function getValidationRules(bool $isUpdateRequest): array
    {
        $rules = [
            "theme" => [
                "string",
                Rule::in(
                    explode(",", config("my_clinic.supported_theme_modes"))
                ),
            ],
            "locale" => [
                "string",
                Rule::in(explode(",", config("my_clinic.supported_locales"))),
            ],
        ];
        if ($isUpdateRequest) {
            // make fields nullable and required only if the other one is not present/null
            $rules["theme"] = array_merge($rules["theme"], [
                "required_without:locale",
                "nullable",
            ]);
            $rules["locale"] = array_merge($rules["locale"], [
                "required_without:theme",
                "nullable",
            ]);
        } else {
            // else in case of adding new UserPreferences, both are required
            $rules["theme"][] = "required";
            $rules["locale"][] = "required";
        }
        return $rules;
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
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\CustomValidationException
     * @throws \App\Exceptions\UpdateRequestForNonExistingObjectException
     */
    public function update(Request $request)
    {
        $input = $this->customValidate(
            $request,
            $this->getValidationRules(true)
        );

        $this->userPreferencesService->update(
            $request->user()->preferences,
            Arr::get($input, "theme"),
            Arr::get($input, "locale")
        );
        return $this->successResponse(status_code: Response::HTTP_NO_CONTENT);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $userPreferences = $request->user()->preferences;

        $this->userPreferencesService->delete($userPreferences);
        return $this->successResponse(status_code: Response::HTTP_NO_CONTENT);
    }
}
