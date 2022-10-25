<?php

namespace Tests\Unit\Users;

use App\Exceptions\EmailAlreadyRegisteredException;
use App\Exceptions\EmailUnauthorizedToRegisterException;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Domain\Users\Actions\UpdateUserAction;
use Domain\Users\Exceptions\RoleNotFoundException;
use Domain\Users\Factories\UserDataFactory;
use Tests\Utils\CustomTestCases\BaseActionTestCaseWithMigrations;

class UpdateUserActionTest extends BaseActionTestCaseWithMigrations
{
    public function test_execution_with_already_registered_email_throws_exception()
    {
        /**
         * @var User $user2
         * @var User $user1
         */
        $user1 = User::query()->find(1);
        $user2 = User::query()->find(2);

        $this->expectException(EmailAlreadyRegisteredException::class);

        $data = UserDataFactory::new()
            ->withEmail($user1->email)
            ->forUpdate();
        // preform update
        $this->action()->execute($user2, $data);
    }

    public function action(): UpdateUserAction
    {
        return app(UpdateUserAction::class);
    }

    public function getSeederClass(): string
    {
        return DatabaseSeeder::class;
    }

    public function test_execution_with_valid_data_is_success(): void
    {
        $this->assertTrue(true);
        // tested in UpdateStaffMemberActionTest
    }

    public function test_execution_with_invalid_data_is_failure(): void
    {
        $this->assertTrue(true);
        // tested in UpdateStaffMemberActionTest
    }

    public function test_execution_with_invalid_role_throws_exception(): void
    {
        /**
         * @var User $user
         */
        $user = User::query()->find(1);

        $this->expectException(RoleNotFoundException::class);
        $this->action()->execute(
            user: $user,
            data: UserDataFactory::new()
                ->fromRandomStaffMember()
                ->withRole("randomRoleSlug")
                ->forUpdate()
        );
    }

    public function test_execution_with_non_staff_email_throws_exception(): void
    {
        /**
         * @var User $user
         */
        $user = User::query()->find(1);

        $this->expectException(EmailUnauthorizedToRegisterException::class);
        $this->action()->execute(
            user: $user,
            data: UserDataFactory::new()
                ->withEmail("randomEmail@nothing.com")
                ->forUpdate()
        );
    }
}
