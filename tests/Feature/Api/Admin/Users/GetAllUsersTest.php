<?php

namespace Tests\Feature\Api\Admin\Users;

use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;

class GetAllUsersTest extends BaseUserApiRequestTest
{
    function getRequestMethod(): string
    {
        return "GET";
    }

    function getRouteName(): string
    {
        return "get-all-users";
    }

    function test_authorized_request()
    {
        $response = $this->makeRequestAuthorizedByUserAbility("admin");
        $response->assertStatus(Response::HTTP_OK)->assertJson(
            fn(AssertableJson $json) => $json
                ->has("data", config("my_clinic.seeded_users_count"))
                ->has(
                    "data.0",
                    fn(AssertableJson $userJson) => $userJson
                        ->hasAll([
                            "id",
                            "email",
                            "role",
                            "phone_number",
                            "created_at",
                            "updated_at",
                        ])
                        // the presence of "email_verified_at" field depends on whether the user's email is verified or not
                        ->etc()
                )
                ->where("status", Response::HTTP_OK)
                ->where("total", config("my_clinic.seeded_users_count"))
                ->where("error", null)
        );
    }
}
