<?php

namespace Tests\Feature\Api\UserPreferences;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Utils\CustomDatabaseSeeders\AllExceptUserPreferencesDBSeeder;
use Tests\Utils\CustomTestCases\BaseApiRequestTestCase;

abstract class BaseUserPreferencesApiRequestTest extends BaseApiRequestTestCase
{
    use DatabaseMigrations;

    protected bool $seed = true;
    protected string $seeder = AllExceptUserPreferencesDBSeeder::class;

    function getMiddleware(): array
    {
        return ["auth:sanctum"];
    }

    public function test_unauthorized_request_returns_error_response()
    {
        $response = $this->makeRequest();
        $response->assertUnauthorized();
    }

    public function test_request_by_unauthorized_user_returns_error_response()
    {
        $response = $this->makeRequest();
        $response->assertUnauthorized();
    }

    /**
     * @test
     */
    public function test_route_has_specified_middleware()
    {
        $this->assertRouteContainsMiddleware();
    }
}
