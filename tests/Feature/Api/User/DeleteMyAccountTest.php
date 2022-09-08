<?php

namespace Tests\Feature\Api\User;

use Symfony\Component\HttpFoundation\Response;

class DeleteMyAccountTest extends BaseUsersApiRequestTest
{
    private int $seeded_dentist_id = 2;
    function getRouteName(): string
    {
        return 'delete-my-account';
    }
    function getRequestMethod(): string
    {
        return 'delete';
    }


    function test_authorized_request()
    {
        $this->setRouteParameters(["user" => $this->seeded_dentist_id]);
        $response = $this->makeRequestAuthorizedByUserAbility("dentist");
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
