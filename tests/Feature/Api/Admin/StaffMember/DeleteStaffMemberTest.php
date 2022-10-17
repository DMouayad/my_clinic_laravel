<?php

namespace Tests\Feature\Api\Admin\StaffMember;

use Domain\StaffMembers\Exceptions\DeletingOnlyAdminStaffMemberException;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;

class DeleteStaffMemberTest extends BaseStaffMemberApiRequestTest
{
    function getRouteName(): string
    {
        return "delete-staff-member";
    }

    function getRequestMethod(): string
    {
        return "DELETE";
    }

    public function test_request_by_unauthorized_user_returns_error_response()
    {
        $this->setRouteParameters(["staff_member" => 2]);

        parent::test_request_by_unauthorized_user_returns_error_response();
    }

    public function test_unauthorized_request_returns_error_response()
    {
        $this->setRouteParameters(["staff_member" => 2]);

        parent::test_unauthorized_request_returns_error_response();
    }

    public function test_authorized_request_returns_success_response()
    {
        $this->setRouteParameters(["staff_member" => 2]);
        $response = $this->makeRequestAuthorizedByUser("admin");
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function test_authorized_request_with_missing_parameter_throws_exception()
    {
        $this->assertThrows(function () {
            $this->makeRequestAuthorizedByUser("admin");
        }, UrlGenerationException::class);
    }

    public function test_authorized_request_with_invalid_staffMember_id()
    {
        // add the id of to-be-deleted StaffMember as a parameter in request url
        $this->setRouteParameters([
            "staff_member" => "NotAnID-Or-IDForNonExistingInstance",
        ]);

        $response = $this->makeRequestAuthorizedByUser("admin");
        $response->assertStatus(Response::HTTP_NOT_FOUND)->assertJson(
            fn(AssertableJson $json) => $json
                ->where("exception", "ModelNotFoundException")
                ->has("message")
                ->etc()
        );
    }

    public function test_deleting_the_only_admin_staffMember_returns_exception()
    {
        // add the id of to-be-deleted StaffMember as a parameter in request url
        $this->setRouteParameters(["staff_member" => 1]);

        $response = $this->makeRequestAuthorizedByUser("admin");
        $response->assertStatus(Response::HTTP_CONFLICT)->assertJson(
            fn(AssertableJson $json) => $json
                ->where("status", Response::HTTP_CONFLICT)
                ->where(
                    "error.exception",
                    DeletingOnlyAdminStaffMemberException::className()
                )
                ->etc()
        );
    }
}
