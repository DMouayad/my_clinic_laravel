<?php

namespace Tests\Feature\Api\Auth;

use App\Exceptions\CustomValidationException;
use App\Exceptions\InvalidEmailCredentialException;
use Database\Seeders\Utils\ProvidesUserSeedingData;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    function test_authorized_request_returns_success_response()
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
                    ])
                )
                ->has(
                    "data.access_token",
                    fn(AssertableJson $accessToken) => $accessToken
                        ->whereType("token", "string")
                        ->has("expires_at")
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

    function test_unauthorized_request_returns_error_response()
    {
        $this->test_request_by_unauthorized_user_returns_error_response();
    }

    function test_request_by_unauthorized_user_returns_error_response()
    {
        // test login for an already logged-in user
        $response = $this->makeRequestAuthorizedByUserAbility(
            "admin",
            data: $this->getValidLoginData()
        );
        $response->assertForbidden();
    }

    function test_route_has_specified_middleware()
    {
        $this->assertRouteContainsMiddleware();
    }

    function test_request_with_invalid_email_returns_exception()
    {
        $response = $this->makeRequest(
            data: [
                "email" => "randomEmail@myclinic.com",
                "password" => "password",
                "device_id" => "randomDeviceId",
            ]
        );
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)->assertJson(
            fn(AssertableJson $json) => $json
                ->where(
                    "error.exception",
                    InvalidEmailCredentialException::className()
                )
                ->where("status", Response::HTTP_UNAUTHORIZED)
                ->etc()
        );
    }

    function test_request_with_missing_data_returns_exception()
    {
        $response = $this->makeRequest();

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->where(
                        "error.exception",
                        CustomValidationException::className()
                    )
                    ->has(
                        "error.description",
                        fn(AssertableJson $message) => $message->hasAll([
                            "email",
                            "password",
                            "device_id",
                        ])
                    )
                    ->etc()
            );
    }
}
