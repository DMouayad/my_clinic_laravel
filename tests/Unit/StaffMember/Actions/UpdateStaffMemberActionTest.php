<?php

    namespace Tests\Unit\StaffMember\Actions;

    use Domain\StaffMembers\Actions\UpdateStaffMemberAction;
    use Domain\StaffMembers\Exceptions\StaffMemberAlreadyExistsException;
    use Domain\StaffMembers\Factories\StaffMemberDataFactory;
    use Domain\Users\Actions\CreateUserAction;
    use Domain\Users\Exceptions\RoleNotFoundException;
    use Domain\Users\Factories\UserDataFactory;
    use Domain\Users\Models\Role;
    use Tests\Utils\Enums\UserRole;
    use TypeError;

    class UpdateStaffMemberActionTest extends BaseStaffMemberActionTest
    {
        public function test_execution_with_an_existing_email_throws_exception(){
            $this->assertModelExists($this->staff_member);
            $this->expectException(StaffMemberAlreadyExistsException::class);
            // setup - create another model
            $another_staff_member = $this->createStaffMember();
            $this->assertModelExists($another_staff_member);

            // act
            $this->action()->execute(
                $this->staff_member,
                StaffMemberDataFactory::new()
                    ->withEmail($another_staff_member->email)
                    ->forUpdate()
            );
        }

        public function action(): UpdateStaffMemberAction{
            return app(UpdateStaffMemberAction::class);
        }

        public function test_execution_with_invalid_data_is_failure(): void{
            $this->expectException(TypeError::class);
            $data = StaffMemberDataFactory::new()->createWithNullAttributes();
            $this->action()->execute($this->staff_member, $data);
        }

        public function test_execution_with_valid_data_is_success(): void{
            //step one - assert was created
            $this->assertModelExists($this->staff_member);
            //step two - setup for update
            $data = StaffMemberDataFactory::new()
                ->withEmail("newEmail@test.com")
                ->withRole(UserRole::admin->name)
                ->forUpdate();
            // act
            $was_updated = $this->action()->execute($this->staff_member, $data);
            // assert
            $this->assertTrue($was_updated);
            $this->assertDatabaseHas("staff_members", [
                "email"   => $data->email,
                "role_id" => Role::getIdWhereSlug($data->role),
            ]);
        }

        public function test_execution_with_invalid_role_slug_throws_exception(){
            $this->expectException(RoleNotFoundException::class);

            $staff_member_data = StaffMemberDataFactory::new()
                ->withRole("RandomRole")
                ->forUpdate();

            $this->action()->execute($this->staff_member, $staff_member_data);
        }

        public function test_execution_with_valid_data_updates_user_data(){
            // setup
            // assert staff member was created
            $this->assertModelExists($this->staff_member);
            // create user for the staff_member
            $user = app(CreateUserAction::class)->execute(
                UserDataFactory::new()
                    ->fromStaffMember($this->staff_member)
                    ->create()
            );
            // refresh cuz StaffMember [user_ui] was updated after creating the user
            $this->staff_member->refresh();
            $this->assertEquals($this->staff_member->user_id, $user->id);

            // act
            $data = StaffMemberDataFactory::new()
                ->withEmail("newEmail@test.com")
                ->withRole(UserRole::admin->name)
                ->forUpdate();
            $this->action()->execute($this->staff_member, $data);

            $this->staff_member->refresh();
            // assert that StaffMember's user was updated with new email and role
            $user = $this->staff_member->user;
            $this->assertEquals($this->staff_member->email, $user->email);
            $this->assertEquals($this->staff_member->role_id, $user->role_id);
        }
    }
