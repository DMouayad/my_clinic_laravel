<?php

namespace Tests\Unit\StaffMember\Actions;

use App\Models\User;
use Domain\StaffMembers\Actions\DeleteStaffMemberAction;
use Domain\StaffMembers\Exceptions\DeletingOnlyAdminStaffMemberException;
use Domain\Users\Actions\CreateUserAction;
use Domain\Users\Factories\UserDataFactory;
use Tests\Utils\Enums\UserRole;
use TypeError;

class DeleteStaffMemberActionTest extends BaseStaffMemberActionTest
{
    private User $adminUser;

    public function test_deleting_only_admin_staff_member_throws_exception()
    {
        $admin_staff = $this->createStaffMember(role: UserRole::admin);
        $this->adminUser = $this->createAdminUser($admin_staff->email);

        $this->expectException(DeletingOnlyAdminStaffMemberException::class);

        $this->action()->execute($admin_staff, $this->adminUser);
    }

    public function createAdminUser(string $email): User
    {
        $user = app(CreateUserAction::class)->execute(
            UserDataFactory::new()
                ->withAdminRole()
                ->withEmail($email)
                ->create()
        );
        return $user;
    }

    public function action(): DeleteStaffMemberAction
    {
        return app(DeleteStaffMemberAction::class);
    }

    public function test_execution_with_invalid_data_is_failure(): void
    {
        $this->expectException(TypeError::class);
        $this->action()->execute(null, null);
    }

    public function test_execution_with_valid_data_is_success(): void
    {
        $admin_staff = $this->createStaffMember(role: UserRole::admin);
        $this->adminUser = $this->createAdminUser($admin_staff->email);

        // act
        $was_deleted = $this->action()->execute(
            $this->staff_member,
            $this->adminUser
        );
        // assert
        $this->assertTrue($was_deleted);
        $this->assertDatabaseMissing("staff_members", [
            "id" => $this->staff_member->id,
        ]);
    }
}
