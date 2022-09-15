<?php

namespace Tests\Feature\Api\Admin\StaffEmail;

use App\Exceptions\RoleNotFoundException;
use App\Exceptions\StaffEmailAlreadyExistsException;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;

class StoreStaffEmailTest extends BaseStaffEmailApiRequestTest
{
    private array $request_data = [
        "email" => "testEmail@gmail.com",
        "role" => "admin",
    ];

    function getRouteName(): string
    {
        return "store-staff-email";
    }

    function getRequestMethod(): string
    {
        return "POST";
    }

    public function test_authorized_request()
    {
        $response = $this->makeRequestAuthorizedByUserAbility(
            "admin",
            $this->request_data
        );

        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has("message")
                ->where("data", null)
                ->where("status", Response::HTTP_CREATED)
        );
    }

    public function test_authorized_request_with_missing_data()
    {
        $response = $this->makeRequestAuthorizedByUserAbility("admin");
        $response
            ->assertJsonValidationErrors([
                "email" => ["The email field is required."],
                "role" => ["The role field is required."],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has("errors", 2)
                    ->has("message")
            );
    }

    public function test_authorized_request_with_invalid_email()
    {
        $invalid_data = [
            "email" => "NotValidEmail",
            "role" => "admin",
        ];
        $response = $this->makeRequestAuthorizedByUserAbility(
            "admin",
            $invalid_data
        );
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertInvalid([
                "email" => ["The email must be a valid email address."],
            ])
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has("errors", 1)
                    ->has("message")
            );
    }

    public function test_authorized_request_with_already_existing_email()
    {
        $already_seeded_data = [
            "email" => "admin@myclinic.com",
            "role" => "admin",
        ];
        $response = $this->makeRequestAuthorizedByUserAbility(
            "admin",
            $already_seeded_data
        );
        $response
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where("status", Response::HTTP_CONFLICT)
                    ->has(
                        "errors.0",
                        fn (AssertableJson $error) => $error
                            ->where(
                                "exception",
                                StaffEmailAlreadyExistsException::className()
                            )->etc()
                    )
            );
    }

    public function test_authorized_request_with_invalid_role()
    {
        $invalid_data = [
            "email" => "testEmail@gmail.com",
            "role" => "someRole",
        ];
        $response = $this->makeRequestAuthorizedByUserAbility(
            "admin",
            $invalid_data
        );
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where("status", Response::HTTP_UNPROCESSABLE_ENTITY)
                    ->has(
                        "errors.0",
                        fn ($error) => $error
                            ->where("exception", RoleNotFoundException::className())
                            ->etc()
                    )
            );
    }
}
