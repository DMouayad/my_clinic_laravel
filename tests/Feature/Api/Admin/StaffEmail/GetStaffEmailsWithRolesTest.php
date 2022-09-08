<?php

namespace Tests\Feature\Api\Admin\StaffEmail;

use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;

class GetStaffEmailsWithRolesTest extends BaseStaffEmailApiRequestTest
{
    function getRouteName(): string
    {
        return "get-staff-emails-with-roles";
    }

    function getRequestMethod(): string
    {
        return "GET";
    }

    public function test_authorized_request()
    {
        $response = $this->makeRequestAuthorizedByUserAbility("admin");
        $seeded_staff_emails_count = 3;
        $response->assertJson(
            fn(AssertableJson $json) => $json
                ->has(
                    "data",
                    $seeded_staff_emails_count,
                    fn($json) => $json->hasAll(["id", "email", "role"])
                )
                ->where("errors", null)
                ->where("status", Response::HTTP_OK)
                ->where("total", $seeded_staff_emails_count)
                ->where("total", $seeded_staff_emails_count)
        );
    }
}
