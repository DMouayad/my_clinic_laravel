<?php

namespace Tests\Feature\Api\Auth;

use App\Exceptions\EmailAlreadyRegisteredException;
use App\Http\Middleware\EnsureStaffEmailProvided;
use App\Services\UserService;
use Database\Seeders\Utils\ProvidesUserSeedingData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\Utils\BaseApiRequestTestCase;
use Tests\Utils\Helpers\TestingUsersHelper;
use Tests\Utils\RolesAndStaffEmailDBSeeders;

class RegisterTest extends BaseApiRequestTestCase
{
    use ProvidesUserSeedingData, RefreshDatabase;

    protected string $seeder = RolesAndStaffEmailDBSeeders::class;

    function getRouteName(): string
    {
        return "api-register";
    }

    function getMiddleware(): array
    {
        return ["guest", EnsureStaffEmailProvided::class];
    }

    function getRequestMethod(): string
    {
        return "POST";
    }

    function test_route_has_specified_middleware()
    {
        $this->assertRouteContainsMiddleware();
    }

    function test_authorized_request()
    {
        $response = $this->makeRequest(data: $this->getValidRegistrationData());
        $response->assertStatus(Response::HTTP_CREATED)->assertJson(
            fn (AssertableJson $json) => $json
                ->where("status", Response::HTTP_CREATED)
                ->has("message")
                ->has(
                    "data",
                    fn ($data) => $data->hasAll(["user", "token"])->has(
                        "user",
                        fn ($user) => $user
                            ->where("id", 1)
                            ->where("name", "testName")
                            ->hasAll([
                                "email",
                                "role",
                                "created_at",
                                "updated_at",
                            ])
                            ->missing("email_verified_at")
                    )
                )
        );
    }

    private function getValidRegistrationData(): array
    {
        return [
            "email" => $this->users_seeding_emails["admin"],
            "name" => "testName",
            "password" => $this->default_password,
        ];
    }

    function test_unauthorized_request()
    {
        $not_staffEmail_email = "randomEmail@myclinic.com";
        $response = $this->makeRequest(
            data: [
                "email" => $not_staffEmail_email,
                "name" => "myName",
                "password" => $this->default_password,
            ]
        );
        $response->assertForbidden()->assertJson(
            fn (AssertableJson $json) => $json
                ->where("status", Response::HTTP_FORBIDDEN)
                ->has("errors", 1)
                ->has(
                    "errors.0",
                    fn (AssertableJson $error) => $error
                        ->whereNot("message", null)
                        ->etc()
                )
        );
    }

    function test_request_by_unauthorized_user()
    {
        $this->createAdminUser();
        $response = $this->makeRequestAuthorizedByUserAbility(
            "admin",
            data: $this->getValidRegistrationData()
        );
        $response->assertForbidden();
    }

    private function createAdminUser()
    {
        $testing_users_helper = new TestingUsersHelper(new UserService());
        return $testing_users_helper->createAdminUser(grant_token: true);
    }

    function test_request_with_missing_email()
    {
        $response = $this->makeRequest();
        $response
            ->assertJsonValidationErrors([
                "email" => ["The email field is required."],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has("errors", 1)
                    ->has("message")
            );
    }

    function test_request_with_missing_name()
    {
        $response = $this->makeRequest(
            data: Arr::only($this->getValidRegistrationData(), "email")
        );
        $response
            ->assertJsonValidationErrors([
                "name" => ["The name field is required."],
                "password" => ["The password field is required."],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has("errors", 2)
                    ->has("message")
            );
    }

    function test_request_with_missing_password()
    {
        $response = $this->makeRequest(
            data: Arr::only($this->getValidRegistrationData(), [
                "email",
                "name",
            ])
        );
        $response
            ->assertJsonValidationErrors([
                "password" => ["The password field is required."],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has("errors", 1)
                    ->has("message")
            );
    }

    public function test_register_with_already_registered_email_returns_exception()
    {
        // register the user for the first time.
        $this->makeRequest($this->getValidRegistrationData());
        // register again
        $response = $this->makeRequest($this->getValidRegistrationData());
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where("status", Response::HTTP_CONFLICT)
                ->has(
                    "errors.0",
                    fn ($error) => $error
                        ->where(
                            "exception",
                            EmailAlreadyRegisteredException::class
                        )
                        ->hasAll(["message", 'exception', 'code', 'description'])
                )
        );
    }
}
