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

    public function test_authorized_request_returns_success_response()
    {
        $response = $this->makeRequestAuthorizedByUserAbility(
            "admin",
            $this->request_data
        );

        $response->assertJson(
            fn(AssertableJson $json) => $json
                ->has("message")
                ->where("data", null)
                ->where("status", Response::HTTP_CREATED)
        );
    }

    public function test_authorized_request_with_missing_data_returns_error()
    {
        $response = $this->makeRequestAuthorizedByUserAbility("admin");
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->has("error.description.email")
                    ->has("error.description.role")
                    ->etc()
            );
    }

    public function test_authorized_request_with_invalid_email_returns_error()
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
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->has("error.description.email")
                    ->where("status", Response::HTTP_UNPROCESSABLE_ENTITY)
                    ->etc()
            );
    }

    public function test_store_already_existing_email_returns_exception()
    {
        $already_seeded_data = [
            "email" => "admin@myclinic.com",
            "role" => "admin",
        ];
        $response = $this->makeRequestAuthorizedByUserAbility(
            "admin",
            $already_seeded_data
        );
        $response->assertStatus(Response::HTTP_CONFLICT)->assertJson(
            fn(AssertableJson $json) => $json
                ->where("status", Response::HTTP_CONFLICT)
                ->where(
                    "error.exception",
                    StaffEmailAlreadyExistsException::className()
                )
                ->etc()
        );
    }

    public function test_authorized_request_with_invalid_role_returns_exception()
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
                fn(AssertableJson $json) => $json
                    ->where("status", Response::HTTP_UNPROCESSABLE_ENTITY)
                    ->where(
                        "error.exception",
                        RoleNotFoundException::className()
                    )
                    ->etc()
            );
    }
}
