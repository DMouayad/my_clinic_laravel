<?php

namespace Tests\Feature\Api\Admin\StaffMember;

use Domain\StaffMembers\Exceptions\DeletingOnlyAdminStaffMemberException;
use Domain\StaffMembers\Models\StaffMember;
use Domain\Users\Models\Role;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Testing\Fluent\AssertableJson;
use Support\Helpers\ClassNameStringifier;
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
        $this->setRouteParameters([
            "staff_member" => $this->get_id_of_non_admin_staff_member(),
        ]);

        parent::test_request_by_unauthorized_user_returns_error_response();
    }

    private function get_id_of_non_admin_staff_member(): int
    {
        return StaffMember::query()
            ->whereNot("role_id", Role::getIdWhereSlug("admin"))
            ->first(["id"])->id;
    }

    public function test_unauthorized_request_returns_error_response()
    {
        $this->setRouteParameters([
            "staff_member" => $this->get_id_of_non_admin_staff_member(),
        ]);

        parent::test_unauthorized_request_returns_error_response();
    }

    public function test_authorized_request_returns_success_response()
    {
        $this->setRouteParameters([
            "staff_member" => $this->get_id_of_non_admin_staff_member(),
        ]);

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
        $this->setRouteParameters([
            "staff_member" => $this->get_id_of_an_admin_staff_member(),
        ]);
        $response = $this->makeRequestAuthorizedByUser("admin");

        $response->assertStatus(Response::HTTP_CONFLICT)->assertJson(
            fn(AssertableJson $json) => $json
                ->where("status", Response::HTTP_CONFLICT)
                ->where(
                    "error.exception",
                    ClassNameStringifier::getClassName(
                        DeletingOnlyAdminStaffMemberException::class
                    )
                )
                ->etc()
        );
    }

    private function get_id_of_an_admin_staff_member(): int
    {
        return StaffMember::query()
            ->where("role_id", Role::getIdWhereSlug("admin"))
            ->first(["id"])->id;
    }
}
