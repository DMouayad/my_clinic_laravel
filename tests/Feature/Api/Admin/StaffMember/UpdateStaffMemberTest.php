<?php

namespace Tests\Feature\Api\Admin\StaffMember;

use Domain\StaffMembers\Exceptions\StaffMemberAlreadyExistsException;
use Domain\Users\Exceptions\RoleNotFoundException;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;

class UpdateStaffMemberTest extends BaseStaffMemberApiRequestTest
{
    private int $admin_staffMember_id = 1;
    private int $dentist_staffMember_id = 2;

    function getRouteName(): string
    {
        return "update-staff-member";
    }

    function getRequestMethod(): string
    {
        return "PUT";
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
        $new_data = ["email" => "updatedEmail@gmail.com", "role" => "admin"];
        $this->setRouteParameters([
            "staff_member" => $this->admin_staffMember_id,
        ]);

        $response = $this->makeRequestAuthorizedByUser("admin", $new_data);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function test_authorized_request_with_missing_parameters_throws_exception()
    {
        $this->assertThrows(function () {
            $this->makeRequestAuthorizedByUser("admin");
        }, UrlGenerationException::class);
    }

    public function test_authorized_request_with_no_data_returns_bad_request_response()
    {
        $this->setRouteParameters([
            "staff_member" => $this->admin_staffMember_id,
        ]);
        $response = $this->makeRequestAuthorizedByUser("admin");
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_authorized_request_with_already_existing_email_returns_exception()
    {
        $this->setRouteParameters([
            "staff_member" => $this->dentist_staffMember_id,
        ]);
        $already_seeded_data = [
            "email" => "admin@myclinic.com",
            "role" => "admin",
        ];

        $response = $this->makeRequestAuthorizedByUser(
            "admin",
            $already_seeded_data
        );
        $response->assertStatus(Response::HTTP_CONFLICT)->assertJson(
            fn(AssertableJson $json) => $json
                ->where("status", Response::HTTP_CONFLICT)
                ->where(
                    "error.exception",
                    StaffMemberAlreadyExistsException::className()
                )
                ->etc()
        );
    }

    public function test_authorized_request_with_invalid_role_returns_exception()
    {
        $invalid_data = [
            "email" => "testEmail@gmail.com",
            "role" => "someRole",
        ];
        $this->setRouteParameters([
            "staff_member" => $this->dentist_staffMember_id,
        ]);
        $response = $this->makeRequestAuthorizedByUser("admin", $invalid_data);
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->where("status", Response::HTTP_UNPROCESSABLE_ENTITY)
                    ->where(
                        "error.exception",
                        RoleNotFoundException::className()
                    )
                    ->etc()
            );
    }
}
