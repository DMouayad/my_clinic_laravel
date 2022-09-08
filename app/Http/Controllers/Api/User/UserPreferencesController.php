<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Resources\UserPreferencesResource;
use App\Models\UserPreferences;
use App\Services\UserPreferencesService;
use Illuminate\Http\Request;
use App\Traits\ProvidesApiJsonResponse;
use App\Traits\ProvidesResourcesJsonResponse;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Controller;

class UserPreferencesController extends Controller
{
    use ProvidesApiJsonResponse, ProvidesResourcesJsonResponse;
    public function __construct(private UserPreferencesService $userPreferencesService)
    {
        $this->middleware(['auth:sanctum']);
        $this->setResource(UserPreferencesResource::class);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated =  $request->validate(
            [
                'user_id' => 'integer|required',
                'theme' => 'string|nullable',
                'language' => 'string|nullable'
            ]
        );

        $instance = $this->userPreferencesService->store(
            $request->user()->id,
            Arr::get($validated, 'theme', 'system'),
            Arr::get($validated, 'language', 'en')
        );
        if ($instance) {
            return $this->successResponse(
                ['user_preferences' => $instance],
                status_code: Response::HTTP_CREATED
            );
        } else {
            return $this->errorResponse(['message' => 'user preferences were not saved!']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserPreferences  $userPreferences
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        return $this->resource($request->user()->preferences);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserPreferences  $userPreferences
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $input = $request->validate(['theme' => 'string|nullable', 'language' => 'string|nullable']);
        $was_deleted = $this->userPreferencesService->update(
            $request->user()->preferences,
            Arr::get($input, 'theme'),
            Arr::get($input, 'language')
        );
        if ($was_deleted) {
            return $this->successResponse(status_code: Response::HTTP_NO_CONTENT);
        } else {
            return $this->errorResponse([
                'message' => 'Failed to update the preferences of user with id ' . $request->user()->id,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserPreferences  $userPreferences
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserPreferences $userPreferences)
    {
        $was_deleted = $this->userPreferencesService->delete($userPreferences);
        if ($was_deleted) {
            return $this->successResponse(status_code: Response::HTTP_NO_CONTENT);
        } else {
            return $this->errorResponse();
        }
    }
}
