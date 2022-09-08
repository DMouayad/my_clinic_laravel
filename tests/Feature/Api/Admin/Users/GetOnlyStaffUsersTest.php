<?php

namespace Tests\Feature\Api\Admin\Users;

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
            fn($json) => $json
                ->has("data", 3)
                ->where("status", Response::HTTP_OK)
                ->where("total", 3)
                ->where("errors", null)
        );
    }
}
