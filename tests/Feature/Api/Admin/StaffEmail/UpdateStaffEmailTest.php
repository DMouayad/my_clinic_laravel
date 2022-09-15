<?php

namespace Tests\Feature\Api\Admin\StaffEmail;

use App\Exceptions\RoleNotFoundException;
use App\Exceptions\StaffEmailAlreadyExistsException;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;

class UpdateStaffEmailTest extends BaseStaffEmailApiRequestTest
{
    private int $admin_staffEmail_id = 1;
    private int $dentist_staffEmail_id = 2;

    function getRouteName(): string
    {
        return "update-staff-email";
    }

    function getRequestMethod(): string
    {
        return "PUT";
    }

    public function test_request_by_unauthorized_user()
    {
        $this->setRouteParameters(["staff_email" => 2]);

        parent::test_request_by_unauthorized_user();
    }

    public function test_unauthorized_request()
    {
        $this->setRouteParameters(["staff_email" => 2]);

        parent::test_unauthorized_request();
    }

    public function test_authorized_request()
    {
        $new_data = ["email" => "updatedEmail@gmail.com", "role" => "admin"];
        $this->setRouteParameters([
            "staff_email" => $this->admin_staffEmail_id,
        ]);

        $response = $this->makeRequestAuthorizedByUserAbility(
            "admin",
            $new_data
        );

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function test_authorized_request_with_missing_parameters_throws_exception()
    {
        $this->assertThrows(function () {
            $this->makeRequestAuthorizedByUserAbility("admin");
        }, UrlGenerationException::class);
    }

    public function test_authorized_request_with_no_data_returns_bad_request_response()
    {
        $this->setRouteParameters([
            "staff_email" => $this->admin_staffEmail_id,
        ]);
        $response = $this->makeRequestAuthorizedByUserAbility("admin");
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_authorized_request_with_already_existing_email()
    {
        $this->setRouteParameters([
            "staff_email" => $this->dentist_staffEmail_id,
        ]);
        $already_seeded_data = [
            "email" => "admin@myclinic.com",
            "role" => "admin",
        ];

        $response = $this->makeRequestAuthorizedByUserAbility(
            "admin",
            $already_seeded_data
        );
        $response
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->where("status", Response::HTTP_CONFLICT)
                    ->has(
                        "errors.0",
                        fn(AssertableJson $error) => $error
                            ->where(
                                "exception",
                                StaffEmailAlreadyExistsException::className()
                            )
                            ->etc()
                    )
            );
    }

    public function test_authorized_request_with_invalid_role()
    {
        $invalid_data = [
            "email" => "testEmail@gmail.com",
            "role" => "someRole",
        ];
        $this->setRouteParameters([
            "staff_email" => $this->dentist_staffEmail_id,
        ]);
        $response = $this->makeRequestAuthorizedByUserAbility(
            "admin",
            $invalid_data
        );
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->where("status", Response::HTTP_UNPROCESSABLE_ENTITY)
                    ->has(
                        "errors.0",
                        fn(AssertableJson $error) => $error
                            ->where(
                                "exception",
                                RoleNotFoundException::className()
                            )
                            ->etc()
                    )
            );
    }
}
