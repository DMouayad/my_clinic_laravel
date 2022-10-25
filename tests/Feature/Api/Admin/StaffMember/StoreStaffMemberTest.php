<?php

namespace Tests\Feature\Api\Admin\StaffMember;

use Domain\StaffMembers\Exceptions\StaffMemberAlreadyExistsException;
use Domain\StaffMembers\Models\StaffMember;
use Domain\Users\Exceptions\RoleNotFoundException;
use Illuminate\Testing\Fluent\AssertableJson;
use Support\Helpers\ClassNameStringifier;
use Symfony\Component\HttpFoundation\Response;

class StoreStaffMemberTest extends BaseStaffMemberApiRequestTest
{
    private array $request_data = [
        "email" => "testEmail@gmail.com",
        "role" => "admin",
    ];

    function getRouteName(): string
    {
        return "store-staff-member";
    }

    function getRequestMethod(): string
    {
        return "POST";
    }

    public function test_authorized_request_returns_success_response()
    {
        $response = $this->makeRequestAuthorizedByUser(
            "admin",
            $this->request_data
        );
        $response->assertJson(
            fn(AssertableJson $json) => $json
                ->has("message")
                ->where("data.email", strtolower($this->request_data["email"]))
                ->where("status", Response::HTTP_CREATED)
        );
    }

    public function test_authorized_request_with_missing_data_returns_error()
    {
        $response = $this->makeRequestAuthorizedByUser("admin");
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
        $response = $this->makeRequestAuthorizedByUser("admin", $invalid_data);
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
            "email" => StaffMember::query()->first()->email,
            "role" => "admin",
        ];
        $response = $this->makeRequestAuthorizedByUser(
            "admin",
            $already_seeded_data
        );
        $response->assertStatus(Response::HTTP_CONFLICT)->assertJson(
            fn(AssertableJson $json) => $json
                ->where("status", Response::HTTP_CONFLICT)
                ->where(
                    "error.exception",
                    ClassNameStringifier::getClassName(
                        StaffMemberAlreadyExistsException::class
                    )
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
        $response = $this->makeRequestAuthorizedByUser("admin", $invalid_data);
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->where("status", Response::HTTP_UNPROCESSABLE_ENTITY)
                    ->where(
                        "error.exception",
                        ClassNameStringifier::getClassName(
                            RoleNotFoundException::class
                        )
                    )
                    ->etc()
            );
    }
}
