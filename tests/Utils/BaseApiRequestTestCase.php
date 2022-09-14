<?php

namespace Tests\Utils;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\Utils\ProvidesUserSeedingData;
use Illuminate\Routing\Route;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

abstract class BaseApiRequestTestCase extends TestCase
{
    use ProvidesUserSeedingData;

    private array $route_parameters = [];

    abstract function test_authorized_request();

    abstract function test_unauthorized_request();

    abstract function test_request_by_unauthorized_user();

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
     * @param string $ability
     * @param array $data
     * @return TestResponse
     */
    protected function makeRequestAuthorizedByUserAbility(
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

    private function getUser(string $ability): User
    {
        $role_id = Role::getIdBySlug($ability);
        $user = User::whereRoleId($role_id)->first();
        $user->createToken("test_token", [$ability])->plainTextToken;
        return $user;
    }
}
