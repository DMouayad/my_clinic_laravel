<?php

namespace Tests\Feature\Api\Auth;

use App\Models\User;
use Database\Seeders\Utils\UserSeedingData;
use DDragon\SanctumRefreshToken\Http\Middleware\ValidateRefreshToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\Utils\CustomTestCases\BaseApiRequestTestCase;

class RequestAccessTokenTest extends BaseApiRequestTestCase
{
    use RefreshDatabase;

    protected bool $seed = true;
    protected $testing_device_id = "testingId";

    function getRouteName(): string
    {
        return "api-access-token";
    }

    function getMiddleware(): array
    {
        return [ValidateRefreshToken::class, "guest"];
    }

    function getRequestMethod(): string
    {
        return "POST";
    }

    function test_unauthorized_request_returns_error_response()
    {
        $response = $this->makeRequest($this->getRequestData());
        $response->assertUnauthorized();
    }

    private function getRequestData(): array
    {
        return ["device_id" => $this->testing_device_id];
    }

    function test_request_by_unauthorized_user_returns_error_response()
    {
        // request a new access token with an access token authorized request
        $response = $this->makeRequestAuthorizedByUser(
            "admin",
            $this->getRequestData()
        );
        $response->assertUnauthorized();
    }

    function test_authorized_request_returns_success_response()
    {
        $login_response = $this->loginUser();
        $refresh_token = $login_response["data"]["refresh_token"];

        $request_token_response = $this->makeRequest(
            $this->getRequestData(),
            headers: [
                "Authorization" => "Bearer " . $refresh_token,
            ]
        );
        $request_token_response->assertStatus(Response::HTTP_OK)->assertJson(
            fn(AssertableJson $json) => $json
                ->has(
                    "data",
                    fn(AssertableJson $data) => $data
                        ->hasAll(["refresh_token", "access_token"])
                        ->has(
                            "access_token",
                            fn(AssertableJson $accessToken) => $accessToken
                                ->whereType("token", "string")
                                ->has("expires_at")
                        )
                )
                ->where("status", Response::HTTP_OK)
                ->has("message")
        );
    }

    private function loginUser()
    {
        return $this->json(
            "post",
            route("api-login"),
            data: [
                "email" => User::query()->first(["email"])->email,
                "password" => UserSeedingData::default_password,
                "device_id" => $this->testing_device_id,
            ]
        );
    }

    function test_route_has_specified_middleware()
    {
        $this->assertRouteContainsMiddleware();
    }
}
