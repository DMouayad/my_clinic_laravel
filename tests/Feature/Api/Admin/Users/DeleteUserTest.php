<?php

namespace Tests\Feature\Api\Admin\Users;

use Symfony\Component\HttpFoundation\Response;

class DeleteUserTest extends BaseUserApiRequestTest
{
    // IDs depend on the order of seeding the users
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

    function test_authorized_request_returns_success_response()
    {
        $this->setRouteParameters(["user" => $this->seeded_dentist_id]);
        $response = $this->makeRequestAuthorizedByUserAbility("admin");
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    function test_unauthorized_request_returns_error_response()
    {
        $this->setRouteParameters(["user" => $this->seeded_dentist_id]);

        parent::test_unauthorized_request_returns_error_response();
    }

    function test_request_by_unauthorized_user_returns_error_response()
    {
        $this->setRouteParameters(["user" => $this->seeded_secretary_id]);

        parent::test_request_by_unauthorized_user_returns_error_response();
    }
}
