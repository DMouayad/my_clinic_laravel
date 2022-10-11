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
                    "data",
                    config("my_clinic.seeded_staff_members_count"),
                    fn($json) => $json->hasAll(["id", "email", "role"])
                )
                ->where("error", null)
                ->where("status", Response::HTTP_OK)
                ->where(
                    "meta.total",
                    config("my_clinic.seeded_staff_members_count")
                )
                ->etc()
        );
    }
}
