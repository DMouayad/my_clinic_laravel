<?php

namespace Tests\Feature\Api\User;

use Symfony\Component\HttpFoundation\Response;

class DeleteMyAccountTest extends BaseUsersApiRequestTest
{
    function getRouteName(): string
    {
        return "delete-my-account";
    }

    function getRequestMethod(): string
    {
        return "DELETE";
    }

    function test_authorized_request()
    {
        $response = $this->makeRequestAuthorizedByUserAbility("admin");
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
