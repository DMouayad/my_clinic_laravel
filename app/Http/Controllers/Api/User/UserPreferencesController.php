<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserPreferencesResource;
use App\Models\CustomError;
use App\Services\UserPreferencesService;
use App\Traits\ProvidesApiJsonResponse;
use App\Traits\ProvidesResourcesJsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class UserPreferencesController extends Controller
{
    use ProvidesApiJsonResponse, ProvidesResourcesJsonResponse;

    public function __construct(
        private readonly UserPreferencesService $userPreferencesService
    ) {
        $this->middleware(["auth:sanctum", "verified"]);
        $this->setResource(UserPreferencesResource::class);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $this->customValidate($request, [
            "theme" => "string|nullable",
            "locale" => "string|nullable",
        ]);

        $instance = $this->userPreferencesService->store(
            $request->user()->id,
            Arr::get($validated, "theme", "system"),
            Arr::get($validated, "locale", "en")
        );
        if ($instance) {
            return $this->successResponse(status_code: Response::HTTP_CREATED);
        } else {
            return $this->errorResponse(
                error: new CustomError("user preferences were not saved!")
            );
        }
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
     * @param \App\Models\UserPreferences $userPreferences
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $input = $this->customValidate($request, [
            "theme" => "string|nullable",
            "locale" => "string|nullable",
        ]);
        if (empty($input)) {
            return $this->errorResponse(
                new CustomError(message: "No data was provided"),
                status_code: Response::HTTP_BAD_REQUEST
            );
        } else {
            $was_updated = $this->userPreferencesService->update(
                $request->user()->preferences,
                Arr::get($input, "theme"),
                Arr::get($input, "locale")
            );
            if ($was_updated) {
                return $this->successResponse(
                    status_code: Response::HTTP_NO_CONTENT
                );
            } else {
                return $this->errorResponse(
                    error: new CustomError(
                        "Failed to update the preferences of user with id " .
                            $request->user()->id
                    )
                );
            }
        }
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
        $was_deleted = $this->userPreferencesService->delete($userPreferences);
        if ($was_deleted) {
            return $this->successResponse(
                status_code: Response::HTTP_NO_CONTENT
            );
        } else {
            return $this->errorResponse();
        }
    }
}
