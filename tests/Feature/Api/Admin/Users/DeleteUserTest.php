<?php

namespace Tests\Feature\Api\Admin\Users;

use Symfony\Component\HttpFoundation\Response;

class DeleteUserTest extends BaseUserApiRequestTest
{
    private int $seeded_dentist_id = 2;
    private int $seeded_secretary_id = 3;

    function getRequestMethod(): string
    {
        return "DELETE";
    }

    function getRouteName(): string
    {
        return "delete-user";
    }

    function test_authorized_request()
    {
        $this->setRouteParameters(["user" => $this->seeded_dentist_id]);
        $response = $this->makeRequestAuthorizedByUserAbility("admin");
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    function test_unauthorized_request()
    {
        $this->setRouteParameters(["user" => $this->seeded_dentist_id]);

        parent::test_unauthorized_request();
    }

    function test_request_by_unauthorized_user()
    {
        $this->setRouteParameters(["user" => $this->seeded_secretary_id]);

        parent::test_request_by_unauthorized_user();
    }
}
