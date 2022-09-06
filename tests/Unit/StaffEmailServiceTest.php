<?php

namespace Tests\Unit;

use App\Exceptions\DeletingOnlyAdminStaffEmailException;
use App\Exceptions\RoleNotFoundException;
use App\Exceptions\StaffEmailAlreadyExistsException;
use Tests\TestCase;
use App\Services\StaffEmailService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\Utils\ProvidesUserSeedingData;

class StaffEmailServiceTest extends TestCase
{
    use  RefreshDatabase, ProvidesUserSeedingData;

    public StaffEmailService $staff_email_service;

    protected $seed = true;
    protected $seeder = RoleSeeder::class;

    protected function setUp(): void
    {
        $this->staff_email_service = new StaffEmailService();
        parent::setUp();
    }
    public function createAdminStaffEmail()
    {
        return $this->staff_email_service->store($this->users_seeding_emails['admin'], 'admin');
    }
    public function createDentistStaffEmail()
    {
        return $this->staff_email_service->store($this->users_seeding_emails['dentist'], 'dentist');
    }
    /**
     * CREATE METHOD TESTS
     */
    public function test_creating_staff_email_with_valid_input()
    {
        $staff_email =  $this->staff_email_service->store($this->users_seeding_emails['admin'], 'admin');
        $this->assertModelExists($staff_email);
    }

    public function test_creating_staff_email_with_invalid_role_slug_throws_exception()
    {
        $this->assertThrows(
            function () {
                $this->staff_email_service->store($this->users_seeding_emails['admin'], 'someSlug');
            },
            RoleNotFoundException::class
        );
    }
    public function test_creating_staff_email_with_already_existing_email_throws_exception()
    {
        $this->assertThrows(
            function () {
                $this->createAdminStaffEmail();
                $this->createAdminStaffEmail();
            },
            StaffEmailAlreadyExistsException::class
        );
    }
    /**
     * END OF CREATE METHOD TESTS
     */
    /**
     * UPDATE METHOD TESTS
     */
    public function test_update_staff_email_with_valid_input()
    {
        $staff_email = $this->createAdminStaffEmail();
        $new_email = 'someEmail@myclinic.com';
        $new_role = 'dentist';

        $updated = $this->staff_email_service->update($staff_email, $new_email, $new_role);
        $this->assertModelExists($updated);
        $this->assertSame($new_email, $updated->email);
        $this->assertSame($new_role, $updated->roleSlug());
    }
    public function test_update_staff_email_with_already_existing_email_throws_exception()
    {
        $this->assertThrows(
            function () {
                $this->createAdminStaffEmail();
                $staffEmail = $this->createDentistStaffEmail();
                $this->staff_email_service->update($staffEmail, email: $this->users_seeding_emails['admin']);
            },
            StaffEmailAlreadyExistsException::class
        );
    }
    public function test_update_staff_email_with_invalid_role_throws_exception()
    {
        $this->assertThrows(
            function () {
                $staffEmail = $this->createDentistStaffEmail();
                $this->staff_email_service->update($staffEmail, role_slug: 'someRole');
            },
            RoleNotFoundException::class
        );
    }
    public function test_deleting_staffEmail()
    {
        $staff_email = $this->createDentistStaffEmail();
        $was_deleted = $this->staff_email_service->delete($staff_email);
        $this->assertModelMissing($staff_email);
        $this->assertTrue($was_deleted);
    }
    public function test_deleting_only_admin_staffEmail_throws_exception()
    {
        $this->assertThrows(function () {
            $staff_email = $this->createAdminStaffEmail();
            $this->staff_email_service->delete($staff_email);
        }, DeletingOnlyAdminStaffEmailException::class);
    }
}
