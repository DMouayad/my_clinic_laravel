<?php

namespace Tests\Feature\Api\UserPreferences;

use App\Exceptions\DeleteAttemptOfNonExistingModelException;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Testing\Fluent\AssertableJson;
use Support\Helpers\ClassNameStringifier;
use Symfony\Component\HttpFoundation\Response;

class DeleteUserPreferencesTest extends BaseUserPreferencesApiRequestTest
{
    // use default db seeder which includes [UserPreferencesSeeder]
    protected string $seeder = DatabaseSeeder::class;

    function getRequestMethod(): string
    {
        return "DELETE";
    }

    function getRouteName(): string
    {
        return "my_preferences.delete";
    }

    function test_authorized_request_returns_success_response()
    {
        $response = $this->makeRequestAuthorizedByUser("admin");
        $response->assertSuccessful();
    }

    function test_deleting_non_existing_userPreferences_returns_error_response()
    {
        $user = $this->getUser("admin");
        // delete the preferences of the user with role admin.
        $this->makeRequestAuthorizedByUser(user: $user);
        // request to delete again
        $user = $user->fresh();
        $response = $this->makeRequestAuthorizedByUser(user: $user);
        $response
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->where(
                        "error.exception",
                        ClassNameStringifier::getClassName(DeleteAttemptOfNonExistingModelException::class)
                    )
                    ->etc()
            );
    }
}
