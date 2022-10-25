<?php

namespace Tests\Unit\Users;

use App\Exceptions\EmailAlreadyRegisteredException;
use App\Exceptions\EmailUnauthorizedToRegisterException;
use Domain\Users\Actions\CreateUserAction;
use Domain\Users\Factories\UserDataFactory;
use Tests\Utils\CustomDatabaseSeeders\RolesAndStaffMemberSeeder;
use Tests\Utils\CustomTestCases\BaseActionTestCaseWithMigrations;
use TypeError;

class CreateUserActionTest extends BaseActionTestCaseWithMigrations
{
    public function test_execution_with_already_registered_email_throws_exception()
    {
        $this->expectException(EmailAlreadyRegisteredException::class);
        $data = UserDataFactory::new()
            ->fromRandomStaffMember()
            ->create();
        // create for 1st time
        $this->action()->execute($data);
        // create new user with the same data
        $this->action()->execute($data);
    }

    public function action(): CreateUserAction
    {
        return app(CreateUserAction::class);
    }

    public function getSeederClass(): string
    {
        return RolesAndStaffMemberSeeder::class;
    }

    public function test_execution_with_valid_data_is_success(): void
    {
        $user = $this->action()->execute(
            UserDataFactory::new()
                ->fromRandomStaffMember()
                ->create()
        );
        $this->assertModelExists($user);
    }

    public function test_execution_with_invalid_data_is_failure(): void
    {
        $this->expectException(TypeError::class);
        $this->action()->execute(
            UserDataFactory::new()->createWithNullAttributes()
        );
    }

    public function test_execution_with_non_staff_email_throws_exception(): void
    {
        $this->expectException(EmailUnauthorizedToRegisterException::class);
        $this->action()->execute(
            UserDataFactory::new()
                ->withEmail("randomEmail@nothing.com")
                ->create()
        );
    }

    public function test_execution_with_valid_data_updates_linked__staff_member_user_id(): void
    {
        $user = $this->action()->execute(
            UserDataFactory::new()
                ->fromRandomStaffMember()
                ->create()
        );
        $this->assertModelExists($user);
        $this->assertDatabaseHas("staff_members", ["user_id" => $user->id]);
    }
}
