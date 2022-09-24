<?php

namespace Tests\Feature\Api\User;

use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;

class GetMyInfoTest extends BaseUsersApiRequestTest
{
    function getRequestMethod(): string
    {
        return "GET";
    }

    function getRouteName(): string
    {
        return "get-my-info";
    }

    function test_authorized_request_returns_success_response()
    {
        $response = $this->makeRequestAuthorizedByUser("admin");
        $response->assertStatus(Response::HTTP_OK)->assertJson(
            fn($json) => $json
                ->has(
                    "data",
                    fn(AssertableJson $userJson) => $userJson
                        ->hasAll([
                            "id",
                            "email",
                            "preferences",
                            "created_at",
                            "updated_at",
                            "role",
                            "phone_number",
                        ])
                        // might have email_verified_at
                        ->etc()
                )
                ->where("status", Response::HTTP_OK)
                ->where("error", null)
        );
    }
}
