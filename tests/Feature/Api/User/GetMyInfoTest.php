<?php

namespace Tests\Feature\Api\User;

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

    function test_authorized_request()
    {
        $response = $this->makeRequestAuthorizedByUserAbility("admin");

        $response->assertStatus(Response::HTTP_OK)->assertJson(
            fn ($json) => $json
                ->has("data")
                ->where("status", Response::HTTP_OK)
                ->where("errors", null)
        );
    }
}
