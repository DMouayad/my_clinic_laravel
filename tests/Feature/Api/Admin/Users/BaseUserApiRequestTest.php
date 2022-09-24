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

    public function test_unauthorized_request_returns_error_response()
    {
        $response = $this->makeRequest();
        $response->assertUnauthorized();
    }

    function test_request_by_unauthorized_user_returns_error_response()
    {
        $response = $this->makeRequestAuthorizedByUser("dentist");
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_route_has_specified_middleware()
    {
        $this->assertRouteContainsMiddleware();
    }
}
