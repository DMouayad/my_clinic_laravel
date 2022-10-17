<?php

namespace Tests\Unit\StaffMember\Actions;

use Database\Seeders\RoleSeeder;
use Domain\StaffMembers\Actions\AddStaffMemberAction;
use Domain\StaffMembers\Factories\StaffMemberDataFactory;
use Domain\Users\Exceptions\RoleNotFoundException;
use Tests\Utils\CustomTestCases\BaseActionTestCaseWithRefreshDatabase;
use TypeError;

class UpdateStaffMemberActionTest extends BaseActionTestCaseWithRefreshDatabase
{
    public function getSeederClass(): string
    {
        return RoleSeeder::class;
    }

    public function test_execution_with_valid_data_is_success(): void
    {
        $staff_member_data = StaffMemberDataFactory::new()->create();
        $action = app(AddStaffMemberAction::class);
        $staff_member = $action->execute($staff_member_data);

        $this->assertModelExists($staff_member);
    }

    public function test_execution_with_invalid_data_is_failure(): void
    {
        $this->expectException(TypeError::class);
        $staff_member_data = StaffMemberDataFactory::new()
            ->nullAttributes()
            ->create();
        $action = app(AddStaffMemberAction::class);
        $action->execute($staff_member_data);
    }

    public function test_execution_with_invalid_role_slug_throws_exception()
    {
        $this->expectException(RoleNotFoundException::class);

        $staff_member_data = StaffMemberDataFactory::new()
            ->withRole("RandomRole")
            ->create();
        $action = app(AddStaffMemberAction::class);
        $action->execute($staff_member_data);
    }
}
