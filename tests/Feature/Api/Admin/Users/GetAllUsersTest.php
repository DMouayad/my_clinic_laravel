<?php

namespace Tests\Feature\Api\Admin\Users;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Testing\Fluent\AssertableJson;

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
            fn (AssertableJson $json) => $json
                ->has("data", 3)
                ->has(
                    'data.0',
                    fn (AssertableJson $userJson) => $userJson->hasAll([
                        'id', 'email', 'role', 'created_at', 'updated_at'
                    ])->etc()
                )
                ->where("status", Response::HTTP_OK)
                ->where("total", 3)
                ->where("errors", null)
        );
    }
}
