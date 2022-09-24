<?php

namespace Tests\Feature\Api\Admin\StaffEmail;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Utils\BaseApiRequestTestCase;

abstract class BaseStaffEmailApiRequestTest extends BaseApiRequestTestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    function getMiddleware(): array
    {
        return ["auth:sanctum", "ability:admin", "verified"];
    }

    public function test_unauthorized_request_returns_error_response()
    {
        $response = $this->makeRequest();
        $response->assertUnauthorized();
    }

    public function test_request_by_unauthorized_user_returns_error_response()
    {
        $response = $this->makeRequestAuthorizedByUser("dentist");
        $response->assertForbidden();
    }

    public function test_route_has_specified_middleware()
    {
        $this->assertRouteContainsMiddleware();
    }
}
