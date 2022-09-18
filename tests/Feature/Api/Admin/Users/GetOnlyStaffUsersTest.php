<?php

namespace Tests\Feature\Api\Admin\Users;

use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;

class GetOnlyStaffUsersTest extends BaseUserApiRequestTest
{
    function getRequestMethod(): string
    {
        return "GET";
    }

    function getRouteName(): string
    {
        return "get-staff-users";
    }

    function test_authorized_request()
    {
        $response = $this->makeRequestAuthorizedByUserAbility("admin");
        $response->assertStatus(Response::HTTP_OK)->assertJson(
            fn(AssertableJson $json) => $json
                ->has(
                    "data.0",
                    fn($userJson) => $userJson
                        ->hasAll([
                            "id",
                            "email",
                            "role",
                            "created_at",
                            "updated_at",
                        ])
                        ->etc()
                )
                ->has(
                    "data.0",
                    fn(AssertableJson $userJson) => $userJson
                        ->has(
                            "role",
                            fn(AssertableJson $roleJson) => $roleJson
                                ->whereNot("name", "Patient")
                                ->whereNot("slug", "patient")
                        )
                        ->etc()
                )
                ->where("status", Response::HTTP_OK)
                ->where("total", config("my_clinic.seeded_staff_users_count"))
                ->where("error", null)
        );
    }
}
