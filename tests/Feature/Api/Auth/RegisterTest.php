<?php

namespace Tests\Feature\Api\Auth;

use App\Api\Admin\StaffMembers\Middleware\EnsureStaffMemberEmailProvided;
use App\Exceptions\EmailAlreadyRegisteredException;
use App\Exceptions\EmailUnauthorizedToRegisterException;
use Domain\StaffMembers\Models\StaffMember;
use Domain\Users\Actions\CreateUserAction;
use Domain\Users\Factories\UserDataFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Support\Helpers\ClassNameStringifier;
use Symfony\Component\HttpFoundation\Response;
use Tests\Utils\CustomDatabaseSeeders\RolesAndStaffMemberSeeder;
use Tests\Utils\CustomTestCases\BaseApiRequestTestCase;

class RegisterTest extends BaseApiRequestTestCase
{
    use DatabaseMigrations;

    protected string $seeder = RolesAndStaffMemberSeeder::class;

    function getRouteName(): string
    {
        return "api-register";
    }

    function getMiddleware(): array
    {
        return ["guest", EnsureStaffMemberEmailProvided::class];
    }

    function getRequestMethod(): string
    {
        return "POST";
    }

    function test_authorized_request_returns_success_response()
    {
        $response = $this->makeRequest(data: $this->getValidRegistrationData());
        $response->assertStatus(Response::HTTP_CREATED)->assertJson(
            fn(AssertableJson $json) => $json
                ->where("status", Response::HTTP_CREATED)
                ->has("message")
                ->has(
                    "data",
                    fn($data) => $data
                        ->hasAll(["user", "refresh_token", "access_token"])
                        ->has(
                            "access_token",
                            fn(AssertableJson $accessToken) => $accessToken
                                ->whereType("token", "string")
                                ->has("expires_at")
                        )
                        ->has(
                            "user",
                            fn(AssertableJson $user) => $user
                                ->where("id", 1)
                                ->where("name", "testName")
                                ->where("email_verified_at", null)
                                ->hasAll([
                                    "email",
                                    "role",
                                    "phone_number",
                                    "created_at",
                                    "updated_at",
                                ])
                        )
                )
        );
    }

    private function getValidRegistrationData(): array
    {
        return [
            "email" => StaffMember::query()->first()->email,
            "name" => "testName",
            "phone_number" => Str::random(10),
            "password" => Str::random(15),
            "device_id" => Str::random(),
        ];
    }

    function test_unauthorized_request_returns_error_response()
    {
        $not_staffMember_email = "randomEmail@myclinic.com";
        $response = $this->makeRequest(
            data: [
                "email" => $not_staffMember_email,
                Arr::except($this->getValidRegistrationData(), "email"),
            ]
        );
        // assert an exception is returned
        $response
            ->assertForbidden()
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->where("status", Response::HTTP_FORBIDDEN)
                    ->where(
                        "error.exception",
                        EmailUnauthorizedToRegisterException::className()
                    )
            );
    }

    function test_request_by_unauthorized_user_returns_error_response()
    {
        // register an already registered user
        $response = $this->makeRequestAuthorizedByUser(
            data: $this->getValidRegistrationData(),
            user: $this->createAdminUser()
        );
        $response->assertForbidden();
    }

    private function createAdminUser(): \App\Models\User
    {
        return app(CreateUserAction::class)->execute(
            UserDataFactory::new()
                ->withAdminRole()
                ->withEmail(StaffMember::findWhereRole("admin")->email)
                ->create()
        );
    }

    public function test_register_with_already_registered_email_returns_exception()
    {
        $register_data = $this->getValidRegistrationData();
        // register the user for the first time.
        $this->makeRequest($register_data);
        // register again
        $response = $this->makeRequest($register_data);
        $response->assertJson(
            fn(AssertableJson $json) => $json
                ->where("status", Response::HTTP_CONFLICT)
                ->has(
                    "error",
                    fn(AssertableJson $error) => $error
                        ->where(
                            "exception",
                            ClassNameStringifier::getClassName(
                                EmailAlreadyRegisteredException::class
                            )
                        )
                        ->hasAll([
                            "message",
                            "exception",
                            "code",
                            "description",
                        ])
                )
        );
    }

    function test_request_with_missing_deviceId()
    {
        $response = $this->makeRequest(
            data: Arr::except($this->getValidRegistrationData(), "device_id")
        );
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->has("error.description.device_id")
                    ->etc()
            );
    }

    function test_request_with_missing_email()
    {
        $response = $this->makeRequest(
            Arr::except($this->getValidRegistrationData(), "email")
        );
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->where("status", Response::HTTP_UNPROCESSABLE_ENTITY)
                    ->has("error.description.email")
                    ->etc()
            );
    }

    function test_request_with_missing_name()
    {
        $response = $this->makeRequest(
            data: Arr::except($this->getValidRegistrationData(), "name")
        );
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->has("error.description.name")
                    ->etc()
            );
    }

    function test_request_with_missing_password()
    {
        $response = $this->makeRequest(
            data: Arr::except($this->getValidRegistrationData(), "password")
        );

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->has("error.description.password")
                    ->etc()
            );
    }

    function test_route_has_specified_middleware()
    {
        $this->assertRouteContainsMiddleware();
    }
}
