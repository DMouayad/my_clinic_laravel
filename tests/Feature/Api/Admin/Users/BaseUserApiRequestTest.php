<?php

namespace Tests\Feature\Api\Admin\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\Utils\BaseApiRequestTestCase;

abstract class BaseUserApiRequestTest extends BaseApiRequestTestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    function getMiddleware(): array
    {
        return ["auth:sanctum", "verified", "ability:admin"];
    }

    public function test_unauthorized_request()
    {
        $response = $this->makeRequest();
        $response->assertUnauthorized();
    }

    public function test_route_has_specified_middleware()
    {
        $this->assertRouteContainsMiddleware();
    }

    function test_request_by_unauthorized_user()
    {
        $response = $this->makeRequestAuthorizedByUserAbility("dentist");
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
