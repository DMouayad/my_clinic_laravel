<?php

namespace Tests\Feature\Api\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Utils\BaseApiRequestTestCase;

abstract class BaseUsersApiRequestTest extends BaseApiRequestTestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    function getMiddleware(): array
    {
        return ["auth:sanctum", "verified"];
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
        $response = $this->makeRequest();
        $response->assertUnauthorized();
    }
}
