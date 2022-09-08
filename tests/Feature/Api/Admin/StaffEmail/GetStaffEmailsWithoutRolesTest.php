<?php

namespace Tests\Feature\Api\Admin\StaffEmail;

use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;

class GetStaffEmailsWithoutRolesTest extends BaseStaffEmailApiRequestTest
{
    public function getRouteName(): string
    {
        return "get-staff-emails";
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
                    fn($json) => $json->missing("role")->hasAll(["id", "email"])
                )
                ->where("errors", null)
                ->where("status", Response::HTTP_OK)
                ->where("total", $seeded_staff_emails_count)
                ->where("total", $seeded_staff_emails_count)
        );
    }
}
