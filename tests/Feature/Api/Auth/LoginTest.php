<?php

namespace Tests\Feature\Api\Auth;

use Database\Seeders\Utils\ProvidesUserSeedingData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
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
                    fn(AssertableJson $data) => $data->hasAll([
                        "user",
                        "access_token",
                        "refresh_token",
                        "expires_in_minutes",
                    ])
                )
                ->has(
                    "data.user",
                    fn(AssertableJson $data) => $data
                        ->hasAll(["role", "preferences"])
                        ->etc()
                )
        );
    }

    private function getValidLoginData(): array
    {
        return [
            "email" => $this->users_seeding_emails["admin"],
            "password" => $this->default_password,
            "device_id" => Str::random(),
        ];
    }

    function test_unauthorized_request()
    {
        $this->test_request_by_unauthorized_user();
    }

    function test_request_by_unauthorized_user()
    {
        // test login for an already logged-in user
        $response = $this->makeRequestAuthorizedByUserAbility(
            "admin",
            data: $this->getValidLoginData()
        );
        $response->assertForbidden();
    }

    function test_request_with_missing_email_returns_error()
    {
        $response = $this->makeRequest([
            "password" => "password",
            "device_id" => "randomDeviceId",
        ]);
        $response
            ->assertJsonValidationErrors([
                "email" => ["The email field is required."],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->has("errors", 1)
                    ->has("errors.email")
                    ->has("message")
            );
    }

    function test_request_with_invalid_email_returns_error()
    {
        $response = $this->makeRequest(
            data: [
                "email" => "randomEmail@myclinic.com",
                "password" => "password",
                "device_id" => "randomDeviceId",
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

    function test_request_with_missing_password_returns_error()
    {
        $response = $this->makeRequest(
            data: Arr::only($this->getValidLoginData(), ["email", "device_id"])
        );
        $response
            ->assertJsonValidationErrors([
                "password" => ["The password field is required."],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->has("errors", 1)
                    ->has("errors.password")
                    ->has("message")
            );
    }

    function test_request_with_missing_data_returns_error()
    {
        $response = $this->makeRequest();
        $response
            ->assertJsonValidationErrors([
                "email" => ["The email field is required."],
                "password" => ["The password field is required."],
                "device_id" => ["The device id field is required."],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->has("errors", 3)
                    ->hasAll([
                        "errors.password",
                        "errors.email",
                        "errors.device_id",
                    ])
                    ->has("message")
            );
    }

    function test_request_with_missing_deviceId()
    {
        $response = $this->makeRequest(
            data: Arr::except($this->getValidLoginData(), "device_id")
        );
        $response
            ->assertJsonValidationErrors([
                "device_id" => ["The device id field is required."],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->has("errors", 1)
                    ->has("errors.device_id")
                    ->has("message")
            );
    }
}
