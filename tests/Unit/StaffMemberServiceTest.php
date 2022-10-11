<?php

namespace Tests\Unit;

use App\Exceptions\DeletingOnlyAdminStaffMemberException;
use App\Exceptions\RoleNotFoundException;
use App\Exceptions\StaffMemberAlreadyExistsException;
use App\Models\StaffMember;
use App\Services\StaffMemberService;
use Database\Seeders\RoleSeeder;
use Database\Seeders\Utils\ProvidesUserSeedingData;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class StaffMemberServiceTest extends TestCase
{
    use DatabaseMigrations, ProvidesUserSeedingData;

    public StaffMemberService $staff_member_service;
    protected bool $seed = true;
    protected string $seeder = RoleSeeder::class;

    /**
     * CREATE METHOD TESTS
     */
    public function test_creating_staff_member_with_valid_input()
    {
        $staff_member = $this->staff_member_service->store(
            $this->users_seeding_emails["admin"],
            "admin"
        );
        $this->assertModelExists($staff_member);
    }

    public function test_creating_staff_member_with_invalid_role_slug_throws_exception()
    {
        $this->assertThrows(function () {
            $this->staff_member_service->store(
                $this->users_seeding_emails["admin"],
                "someSlug"
            );
        }, RoleNotFoundException::class);
    }

    public function test_creating_staff_member_with_already_existing_email_throws_exception()
    {
        $this->assertThrows(function () {
            $this->createAdminStaffMember();
            $this->createAdminStaffMember();
        }, StaffMemberAlreadyExistsException::class);
    }

    public function createAdminStaffMember(): StaffMember
    {
        return $this->staff_member_service->store(
            $this->users_seeding_emails["admin"],
            "admin"
        );
    }

    /**
     * UPDATE METHOD TESTS
     */
    public function test_update_staff_member_with_valid_input()
    {
        $staff_member = $this->createAdminStaffMember();
        $new_email = "someEmail@myclinic.com";
        $new_role = "dentist";

        $updated = $this->staff_member_service->update(
            $staff_member,
            $new_email,
            $new_role
        );
        $this->assertModelExists($updated);
        $this->assertSame($new_email, $updated->email);
        $this->assertSame($new_role, $updated->roleSlug());
    }

    public function test_update_staff_member_with_already_existing_email_throws_exception()
    {
        $this->assertThrows(function () {
            $this->createAdminStaffMember();
            $staffMember = $this->createDentistStaffMember();
            $this->staff_member_service->update(
                $staffMember,
                email: $this->users_seeding_emails["admin"]
            );
        }, StaffMemberAlreadyExistsException::class);
    }

    /**
     * END OF CREATE METHOD TESTS
     */

    public function createDentistStaffMember()
    {
        return $this->staff_member_service->store(
            $this->users_seeding_emails["dentist"],
            "dentist"
        );
    }

    public function test_update_staff_member_with_invalid_role_throws_exception()
    {
        $this->assertThrows(function () {
            $staffMember = $this->createDentistStaffMember();
            $this->staff_member_service->update(
                $staffMember,
                role_slug: "someRole"
            );
        }, RoleNotFoundException::class);
    }

    public function test_deleting_staffMember_completes_successfully()
    {
        // setup
        $staff_member = $this->createDentistStaffMember();

        // act
        $was_deleted = $this->staff_member_service->delete($staff_member);
        $this->assertModelMissing($staff_member);
        $this->assertTrue($was_deleted);
    }

    public function test_deleting_only_admin_staffMember_throws_exception()
    {
        $this->assertThrows(function () {
            $staff_member = $this->createAdminStaffMember();
            $this->staff_member_service->delete($staff_member);
        }, DeletingOnlyAdminStaffMemberException::class);
    }

    protected function setUp(): void
    {
        $this->staff_member_service = new StaffMemberService();
        parent::setUp();
    }
}
