<?php

namespace Tests\Feature\Api\Admin\StaffEmail;

use App\Exceptions\DeletingOnlyAdminStaffEmailException;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteStaffEmailTest extends BaseStaffEmailApiRequestTest
{
    function getRouteName(): string
    {
        return "delete-staff-email";
    }

    function getRequestMethod(): string
    {
        return "DELETE";
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
        $this->setRouteParameters(["staff_email" => 2]);
        $response = $this->makeRequestAuthorizedByUserAbility("admin");
        $response->assertStatus(Response::HTTP_OK)->assertJson(
            fn (AssertableJson $json) => $json
                ->where("data", null)
                ->where("status", Response::HTTP_OK)
                ->missing("errors")
                ->has("message")
        );
    }

    public function test_authorized_request_with_missing_parameter_throws_exception()
    {
        $this->assertThrows(function () {
            $this->makeRequestAuthorizedByUserAbility("admin");
        }, UrlGenerationException::class);
    }

    public function test_authorized_request_with_invalid_staffEmail_id()
    {
        $this->setRouteParameters([
            "staff_email" => "NotAnID-Or-IDForNonExistingInstance",
        ]);

        $response = $this->makeRequestAuthorizedByUserAbility("admin");
        $response->assertStatus(Response::HTTP_NOT_FOUND)->assertJson(
            fn (AssertableJson $json) => $json
                ->where("exception", NotFoundHttpException::class)
                ->has("message")
                ->etc()
        );
    }

    public function test_authorized_request_of_deleting_the_only_admin_staffEmail()
    {
        $this->setRouteParameters(["staff_email" => 1]);

        $response = $this->makeRequestAuthorizedByUserAbility("admin");
        var_dump($response->content());
        $response
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where("status", Response::HTTP_CONFLICT)
                    ->has(
                        "errors.0",
                        fn (AssertableJson $error) => $error
                            ->where(
                                "exception",
                                DeletingOnlyAdminStaffEmailException::class
                            )->etc()
                    )
            );
    }
}
