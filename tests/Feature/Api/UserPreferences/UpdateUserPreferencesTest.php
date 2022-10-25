<?php

namespace Tests\Feature\Api\UserPreferences;

use App\Exceptions\CustomValidationException;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use Support\Helpers\ClassNameStringifier;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserPreferencesTest extends BaseUserPreferencesApiRequestTest
{
    protected string $seeder = DatabaseSeeder::class;

    function getRequestMethod(): string
    {
        return "PUT";
    }

    function getRouteName(): string
    {
        return "my_preferences.update";
    }

    function test_authorized_request_returns_success_response()
    {
        $response = $this->makeRequestAuthorizedByUser(
            "admin",
            data: $this->get_valid_request_data()
        );
        $response->assertNoContent();
    }

    private function get_valid_request_data(): array
    {
        return [
            "locale" => "en",
            "theme" => "dark",
        ];
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

    public function test_request_with_no_data_returns_validation_exception()
    {
        $response = $this->makeRequestAuthorizedByUser("admin");
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->where("error.description", [
                        "theme" => [
                            "The theme field is required when locale is not present.",
                        ],
                        "locale" => [
                            "The locale field is required when theme is not present.",
                        ],
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

    function test_updating_non_existing_preferences_returns_success()
    {
        DB::table("user_preferences")->delete();
        // assert table is is empty
        $this->assertDatabaseCount("user_preferences", 0);
        // make delete request

        $response = $this->makeRequestAuthorizedByUser(
            "admin",
            data: ["theme" => "system"]
        );
        $response->assertSuccessful();
    }
}
