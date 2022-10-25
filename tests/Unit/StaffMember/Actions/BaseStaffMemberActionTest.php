<?php

namespace Tests\Unit\StaffMember\Actions;

use Database\Seeders\RoleSeeder;
use Domain\StaffMembers\Actions\AddStaffMemberAction;
use Domain\StaffMembers\Factories\StaffMemberDataFactory;
use Domain\StaffMembers\Models\StaffMember;
use Tests\Utils\CustomTestCases\BaseActionTestCaseWithMigrations;
use Tests\Utils\Enums\UserRole;

abstract class BaseStaffMemberActionTest extends
    BaseActionTestCaseWithMigrations
{
    protected StaffMember $staff_member;

    public function getSeederClass(): string
    {
        return RoleSeeder::class;
    }

    protected function setUp(bool $should_create_staff_member = true): void
    {
        parent::setUp();
        if ($should_create_staff_member) {
            $this->staff_member = $this->createStaffMember();
        }
    }

    public function createStaffMember(
        ?string $email = null,
        ?UserRole $role = null
    ): StaffMember {
        $staff_member_data = StaffMemberDataFactory::new()
            ->withEmail($email)
            ->withRole($role?->name)
            ->create();
        $action = app(AddStaffMemberAction::class);
        return $action->execute($staff_member_data);
    }
}
