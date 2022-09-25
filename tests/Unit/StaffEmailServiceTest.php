<?php

namespace Tests\Unit;

use App\Exceptions\DeletingOnlyAdminStaffEmailException;
use App\Exceptions\RoleNotFoundException;
use App\Exceptions\StaffEmailAlreadyExistsException;
use App\Models\StaffEmail;
use App\Models\StaffEmailUser;
use App\Services\StaffEmailService;
use App\Services\UserService;
use Database\Seeders\RoleSeeder;
use Database\Seeders\Utils\ProvidesUserSeedingData;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Utils\Enums\UserRole;
use Tests\Utils\Helpers\TestingUsersHelper;

class StaffEmailServiceTest extends TestCase
{
    use DatabaseMigrations, ProvidesUserSeedingData;

    public StaffEmailService $staff_email_service;
    protected bool $seed = true;
    protected string $seeder = RoleSeeder::class;

    /**
     * CREATE METHOD TESTS
     */
    public function test_creating_staff_email_with_valid_input()
    {
        $staff_email = $this->staff_email_service->store(
            $this->users_seeding_emails["admin"],
            "admin"
        );
        $this->assertModelExists($staff_email);
    }

    public function test_creating_staff_email_with_invalid_role_slug_throws_exception()
    {
        $this->assertThrows(function () {
            $this->staff_email_service->store(
                $this->users_seeding_emails["admin"],
                "someSlug"
            );
        }, RoleNotFoundException::class);
    }

    public function test_creating_staff_email_with_already_existing_email_throws_exception()
    {
        $this->assertThrows(function () {
            $this->createAdminStaffEmail();
            $this->createAdminStaffEmail();
        }, StaffEmailAlreadyExistsException::class);
    }

    public function createAdminStaffEmail(): StaffEmail
    {
        return $this->staff_email_service->store(
            $this->users_seeding_emails["admin"],
            "admin"
        );
    }

    /**
     * UPDATE METHOD TESTS
     */
    public function test_update_staff_email_with_valid_input()
    {
        $staff_email = $this->createAdminStaffEmail();
        $new_email = "someEmail@myclinic.com";
        $new_role = "dentist";

        $updated = $this->staff_email_service->update(
            $staff_email,
            $new_email,
            $new_role
        );
        $this->assertModelExists($updated);
        $this->assertSame($new_email, $updated->email);
        $this->assertSame($new_role, $updated->roleSlug());
    }

    public function test_update_staff_email_with_already_existing_email_throws_exception()
    {
        $this->assertThrows(function () {
            $this->createAdminStaffEmail();
            $staffEmail = $this->createDentistStaffEmail();
            $this->staff_email_service->update(
                $staffEmail,
                email: $this->users_seeding_emails["admin"]
            );
        }, StaffEmailAlreadyExistsException::class);
    }

    /**
     * END OF CREATE METHOD TESTS
     */

    public function createDentistStaffEmail()
    {
        return $this->staff_email_service->store(
            $this->users_seeding_emails["dentist"],
            "dentist"
        );
    }

    public function test_update_staff_email_with_invalid_role_throws_exception()
    {
        $this->assertThrows(function () {
            $staffEmail = $this->createDentistStaffEmail();
            $this->staff_email_service->update(
                $staffEmail,
                role_slug: "someRole"
            );
        }, RoleNotFoundException::class);
    }

    public function test_deleting_staffEmail_completes_successfully()
    {
        // setup
        $staff_email = $this->createDentistStaffEmail();
        $this->createStaffEmailUser($staff_email->id, UserRole::dentist);
        // act
        $was_deleted = $this->staff_email_service->delete($staff_email);
        $this->assertModelMissing($staff_email);
        $this->assertTrue($was_deleted);
    }

    /** Creates a User and StaffEmailUser for the specified StaffEmail
     *
     * @param int $staff_email_id
     * @param \Tests\Utils\Enums\UserRole $role
     * @return void
     * @throws \App\Exceptions\EmailAlreadyRegisteredException
     * @throws \App\Exceptions\PhoneNumberAlreadyUsedException
     */
    private function createStaffEmailUser(int $staff_email_id, UserRole $role)
    {
        $users_helper = new TestingUsersHelper(new UserService());
        $user = $users_helper->createUserByRole($role);
        StaffEmailUser::create([
            "staff_email_id" => $staff_email_id,
            "user_id" => $user->id,
        ]);
    }

    public function test_deleting_only_admin_staffEmail_throws_exception()
    {
        $this->assertThrows(function () {
            $staff_email = $this->createAdminStaffEmail();
            $this->staff_email_service->delete($staff_email);
        }, DeletingOnlyAdminStaffEmailException::class);
    }

    protected function setUp(): void
    {
        $this->staff_email_service = new StaffEmailService();
        parent::setUp();
    }
}
