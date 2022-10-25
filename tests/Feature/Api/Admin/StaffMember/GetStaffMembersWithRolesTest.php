<?php

namespace Tests\Feature\Api\Admin\StaffMember;

use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;

class GetStaffMembersWithRolesTest extends BaseStaffMemberApiRequestTest
{
    function getRouteName(): string
    {
        return "staff-members-with-roles";
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
                ->has(
                    "data.0",
                    fn($json) => $json->hasAll([
                        "id",
                        "email",
                        "role",
                        "created_at",
                    ])
                )
                ->where("error", null)
                ->where("status", Response::HTTP_OK)
                ->etc()
        );
    }
}
