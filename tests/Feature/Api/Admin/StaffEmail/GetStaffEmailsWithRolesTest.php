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
        $response->assertJson(
            fn(AssertableJson $json) => $json
                ->has(
                    "data",
                    config("my_clinic.seeded_staff_emails_count"),
                    fn($json) => $json->hasAll(["id", "email", "role"])
                )
                ->where("error", null)
                ->where("status", Response::HTTP_OK)
                ->where("total", config("my_clinic.seeded_staff_emails_count"))
        );
    }
}
