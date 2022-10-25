<?php

namespace Tests\Feature\Api\Admin\StaffMember;

use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;

class GetStaffMembersWithoutRolesTest extends BaseStaffMemberApiRequestTest
{
    public function getRouteName(): string
    {
        return "staff-members";
    }

    function getRequestMethod(): string
    {
        return "GET";
    }

    public function test_authorized_request_returns_success_response()
    {
        $response = $this->makeRequestAuthorizedByUser("admin");

        $response->assertJson(
            fn(AssertableJson $json) => $json
                ->where("status", Response::HTTP_OK)
                ->where("error", null)
                ->has(
                    "data.0",
                    fn($json) => $json
                        ->missing("role")
                        ->hasAll(["id", "email", "created_at"])
                )
                ->etc()
        );
    }
}
