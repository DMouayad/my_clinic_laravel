<?php

namespace Tests\Feature\Api\Auth;

use Database\Seeders\Utils\ProvidesUserSeedingData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\Utils\BaseApiRequestTestCase;

class LoginTest extends BaseApiRequestTestCase
{
    protected bool $seed = true;
    use ProvidesUserSeedingData, RefreshDatabase;

    function getRouteName(): string
    {
        return "api-login";
    }

    function getMiddleware(): array
    {
        return ["guest"];
    }

    function getRequestMethod(): string
    {
        return "POST";
    }

    function test_route_has_specified_middleware()
    {
        $this->assertRouteContainsMiddleware();
    }

    function test_authorized_request()
    {
        $response = $this->makeRequest(data: $this->getValidLoginData());
        $response->assertStatus(Response::HTTP_OK)->assertJson(
            fn(AssertableJson $json) => $json
                ->where("status", Response::HTTP_OK)
                ->has("message")
                ->has(
                    "data",
                    fn($data) => $data
                        ->hasAll(["id", "token", "role"])
                        ->where("id", 1)
                        ->where("role", "admin")
                )
        );
    }

    private function getValidLoginData(): array
    {
        return [
            "email" => $this->users_seeding_emails["admin"],
            "password" => $this->default_password,
        ];
    }

    function test_unauthorized_request()
    {
        $this->test_request_by_unauthorized_user();
    }

    function test_request_by_unauthorized_user()
    {
        // test login for an already logged in user
        $response = $this->makeRequestAuthorizedByUserAbility(
            "admin",
            data: $this->getValidLoginData()
        );
        $response->assertForbidden();
    }

    function test_request_with_missing_email()
    {
        $response = $this->makeRequest();
        $response
            ->assertJsonValidationErrors([
                "email" => ["The email field is required."],
                "password" => ["The password field is required."],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->has("errors", 2)
                    ->has("message")
            );
    }

    function test_request_with_invalid_email()
    {
        $response = $this->makeRequest(
            data: [
                "email" => "randomEmail@myclinic.com",
                "password" => "password",
            ]
        );
        $response
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->has("errors", 1)
                    ->where("status", Response::HTTP_UNAUTHORIZED)
            );
    }

    function test_request_with_missing_password()
    {
        $response = $this->makeRequest(
            data: Arr::only($this->getValidLoginData(), "email")
        );
        $response
            ->assertJsonValidationErrors([
                "password" => ["The password field is required."],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->has("errors", 1)
                    ->has("message")
            );
    }
}
