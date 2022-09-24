<?php

namespace Tests\Utils;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\Utils\ProvidesUserSeedingData;
use Illuminate\Routing\Route;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Base Test Case for testing an API endpoint.
 *
 * Provides helper methods for testing route's middleware, authorization and parameters.
 * Provides methods to make a request with no auth or with sanctum auth
 */
abstract class BaseApiRequestTestCase extends TestCase
{
    use ProvidesUserSeedingData;

    private array $route_parameters = [];

    /**
     * Test that making an authorized request to the api endpoint, with valid data, RETURNS a success response
     */
    abstract function test_authorized_request_returns_success_response();

    /**
     * Test that making a request to the api endpoint with no authorization Returns an error response
     */
    abstract function test_unauthorized_request_returns_error_response();

    /**
     * Test that making a valid request to the api endpoint but by unauthorized user Returns an error response
     */
    abstract function test_request_by_unauthorized_user_returns_error_response();

    public function assertRouteContainsMiddleware(): void
    {
        $route = $this->getRouteByName($this->getRouteName());

        foreach ($this->getMiddleware() as $name) {
            $this->assertContains(
                $name,
                $route->middleware(),
                "Route doesn't contain middleware [{$name}]"
            );
        }
    }

    /**
     * @param string $route_name
     * @return Route
     */
    public function getRouteByName(string $route_name): Route
    {
        $routes = \Illuminate\Support\Facades\Route::getRoutes();

        /** @var Route $route */
        $route = $routes->getByName($route_name);

        if (!$route) {
            $this->fail("Route with name [{$route_name}] not found!");
        }

        return $route;
    }

    abstract function getRouteName(): string;

    abstract function getMiddleware(): array;

    /**
     * @return array
     */
    public function getRouteParameters(): array
    {
        return $this->route_parameters;
    }

    /**
     * @param array $route_parameters
     */
    public function setRouteParameters(array $route_parameters): void
    {
        $this->route_parameters = $route_parameters;
    }

    /**
     * @param array $data
     * @param array $headers
     * @return TestResponse
     */
    protected function makeRequest(
        array $data = [],
        array $headers = []
    ): TestResponse {
        return $this->json(
            $this->getRequestMethod(),
            $this->getRouteUri(),
            $data,
            headers: ["Accept" => "application/json", ...$headers]
        );
    }

    abstract function getRequestMethod(): string;

    public function getRouteUri(): string
    {
        return route($this->getRouteName(), $this->route_parameters);
    }

    /**
     * Make a request to the route specified with [getRouteName]
     *
     * [Sanctum::actingAs] is called with the provided user.
     * if user is null, a user role must be provided to get
     * a user with same role from the database.
     *
     * @param string $ability
     * @param array $data
     * @return TestResponse
     */
    protected function makeRequestAuthorizedByUser(
        string $ability,
        array $data = []
    ): TestResponse {
        Sanctum::actingAs($this->getUser($ability), [$ability]);

        return $this->json(
            $this->getRequestMethod(),
            $this->getRouteUri(),
            $data,
            headers: ["Accept" => "application/json"]
        );
    }

    public function getUser(string $ability): User
    {
        $role_id = Role::getIdBySlug($ability);
        $user = User::whereRoleId($role_id)->first();
        $user->createToken("test_token", [$ability])->plainTextToken;
        return $user;
    }
}
