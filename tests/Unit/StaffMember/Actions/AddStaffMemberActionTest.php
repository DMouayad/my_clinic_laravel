<?php

namespace Tests\Unit\StaffMember\Actions;

use Domain\StaffMembers\Actions\AddStaffMemberAction;
use Domain\StaffMembers\Exceptions\StaffMemberAlreadyExistsException;
use Domain\StaffMembers\Factories\StaffMemberDataFactory;
use Domain\Users\Exceptions\RoleNotFoundException;
use Tests\Utils\Enums\UserRole;
use TypeError;

class AddStaffMemberActionTest extends BaseStaffMemberActionTest
{
    public function test_execution_with_invalid_role_slug_throws_exception()
    {
        $this->expectException(RoleNotFoundException::class);
        $this->createStaffMember(role: UserRole::invalid);
    }

    public function test_execution_with_an_existing_email_throws_exception()
    {
        $this->expectException(StaffMemberAlreadyExistsException::class);
        // setup
        $data_factory = StaffMemberDataFactory::new();
        $first_data = $data_factory->forCreate();
        // create for 1st time
        $staff_member = $this->action()->execute($first_data);
        $this->assertModelExists($staff_member);
        // create for 2nd time
        $this->action()->execute(
            $data_factory->withEmail($first_data->email)->forCreate()
        );
    }

    public function action(): AddStaffMemberAction
    {
        return app(AddStaffMemberAction::class);
    }

    protected function setUp(bool $should_create_staff_member = false): void
    {
        parent::setUp($should_create_staff_member);
    }

    public function test_execution_with_valid_data_is_success(): void
    {
        $staff_member_data = StaffMemberDataFactory::new()->create();

        $staff_member = $this->action()->execute($staff_member_data);

        $this->assertModelExists($staff_member);
    }

    public function test_execution_with_invalid_data_is_failure(): void
    {
        $this->expectException(TypeError::class);
        $staff_member_data = StaffMemberDataFactory::new()->createWithNullAttributes();
        $this->action()->execute($staff_member_data);
    }
}
