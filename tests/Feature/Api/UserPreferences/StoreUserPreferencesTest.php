<?php

namespace Tests\Feature\Api\UserPreferences;

use App\Exceptions\CustomValidationException;
use Illuminate\Testing\Fluent\AssertableJson;
use Support\Helpers\ClassNameStringifier;
use Symfony\Component\HttpFoundation\Response;

class StoreUserPreferencesTest extends BaseUserPreferencesApiRequestTest
{
    function getRequestMethod(): string
    {
        return "Post";
    }

    function getRouteName(): string
    {
        return "my_preferences.add";
    }

    function test_authorized_request_returns_success_response()
    {
        $response = $this->makeRequestAuthorizedByUser(
            "admin",
            data: $this->get_valid_request_data()
        );
        $response->assertCreated();
        $response->assertExactJson([
            "status" => 201,
            "message" => null,
            "data" => null,
        ]);
    }

    private function get_valid_request_data(): array
    {
        return [
            "locale" => "en",
            "theme" => "dark",
        ];
    }

    public function test_store_userPreferences_for_user_with_preferences_updates_existing_ones()
    {
        $user = $this->getUser("admin");
        // save preferences for first time
        $this->makeRequestAuthorizedByUser(
            data: $this->get_valid_request_data(),
            user: $user
        );
        $other_data = [
            "theme" => "light",
            "locale" => "ar",
        ];
        // add preferences for the same user
        $response = $this->makeRequestAuthorizedByUser(
            data: $other_data,
            user: $user
        );
        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function test_request_with_no_data_returns_validation_exception()
    {
        $response = $this->makeRequestAuthorizedByUser("admin");
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->where("error.description", [
                        "theme" => ["The theme field is required."],
                        "locale" => ["The locale field is required."],
                    ])
                    ->where(
                        "error.exception",
                        ClassNameStringifier::getClassName(
                            CustomValidationException::class
                        )
                    )
                    ->etc()
            );
    }

    public function test_request_with_invalid_data_returns_error_response()
    {
        // make request with invalid locale and theme values.
        // Note: supported locales and themes can be found in [config/my_clinic] file
        $response = $this->makeRequestAuthorizedByUser(
            "admin",
            data: ["theme" => "randomString", "locale" => "randomString"]
        );
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->where("error.description", [
                        "theme" => ["The selected theme is invalid."],
                        "locale" => ["The selected locale is invalid."],
                    ])
                    ->where(
                        "error.exception",
                        ClassNameStringifier::getClassName(
                            CustomValidationException::class
                        )
                    )
                    ->etc()
            );
    }
}
