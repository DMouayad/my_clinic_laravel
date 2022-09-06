<?php

namespace Tests\Unit;


use App\Exceptions\EmailAlreadyRegisteredException;
use App\Exceptions\UnauthorizedToDeleteUserException;
use App\Exceptions\UserDoesntMatchHisStaffEmailException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RoleSeeder;
use Database\Seeders\StaffEmailSeeder;
use Illuminate\Database\Seeder;
use App\Services\UserService;


use Tests\Utils\Traits\ProvidesUsersForTesting;


class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([RoleSeeder::class,  StaffEmailSeeder::class]);
    }
}

class UserServiceTest extends TestCase
{
    use  RefreshDatabase, ProvidesUsersForTesting;

    private UserService $userService;
    protected $seed = true;
    protected $seeder = DatabaseSeeder::class;

    protected function setUp(): void
    {
        $this->userService = new UserService();
        parent::setUp();
    }
    private $valid_user_credentials = [
        'email' => 'admin@myclinic.com',
        'name' => 'admin1',
        'password' => 'clinic123',
    ];

    public function test_user_creation_with_valid_credentials()
    {
        $user =  $this->userService->createNewUser(
            $this->valid_user_credentials['email'],
            $this->valid_user_credentials['name'],
            $this->valid_user_credentials['password']
        );
        $this->assertModelExists($user);
    }

    public function test_create_user_with_valid_credentials()
    {
        $user = $this->createAdminUser();
        $this->assertModelExists($user);
    }
    public function test_creating_new_user_creates_staff_email_user()
    {
        $user = $this->createAdminUser();

        $this->assertDatabaseHas(
            'staff_email_user',
            ['user_id' => $user->id, 'staff_email_id' => $user->staffEmail->id]
        );
    }
    public function test_creating_user_with_existing_email_throws_exception()
    {
        $this->assertThrows(function () {
            $this->userService->createNewUser(
                $this->valid_user_credentials['email'],
                $this->valid_user_credentials['name'],
                $this->valid_user_credentials['password']
            );
            $this->userService->createNewUser(
                $this->valid_user_credentials['email'],
                $this->valid_user_credentials['name'],
                $this->valid_user_credentials['password']
            );
        }, EmailAlreadyRegisteredException::class);
    }
    public function test_updating_user_role_and_email()
    {
        $new_email = 'updatedEmail@myclinic.com';
        $user = $this->createAdminUser();
        // user's staffEmail must be updated before updating the user
        $user->staffEmail->update(['email' => $new_email, 'role_id' => 2]);
        $updated = $this->userService->update($user,  2,  $new_email);
        // assert user date was updated in the db
        $this->assertDatabaseHas(
            'users',
            ['id' => $updated->id, 'role_id' => $updated->role_id, 'email' => $new_email]
        );
    }
    public function test_updating_user_with_role_different_from_his_staffEmail_throws_exception()
    {
        $this->assertThrows(function () {

            $new_role_id = 2;
            $user = $this->createAdminUser();
            $this->userService->update($user,  role_id: $new_role_id);
        }, UserDoesntMatchHisStaffEmailException::class);
    }
    public function test_updating_user_with_email_different_from_his_staffEmail_throws_exception()
    {
        $this->assertThrows(function () {

            $new_email = 'newEmail@myclinic.com';
            $user = $this->createAdminUser();
            $this->userService->update($user,  email: $new_email);
        }, UserDoesntMatchHisStaffEmailException::class);
    }
    public function test_updating_user_email_throws_exception_if_already_exists()
    {
        $this->assertThrows(function () {
            $user1 = $this->createDentistUser();
            $user2 = $this->createSecretaryUser();
            $this->userService->update($user1, email: $user2->email);
        }, EmailAlreadyRegisteredException::class);
    }
    public function test_updating_user_email_revokes_his_tokens()
    {
        $new_email = 'updatedEmail@myclinic.com';
        $user = $this->createAdminUser(store_token: true);
        // assert user has one access token
        $this->assertSame(1, $user->tokens->count());

        $user->staffEmail->update(['email' => $new_email]);

        // updates user's email and role_id
        $updated = $this->userService->update($user, email: $new_email);
        // get a fresh instance so tokens relationship will be updated
        $updated = $updated->fresh();
        // expects user's tokens have been deleted. 
        $this->assertSame(0, $updated->tokens->count());
    }
    public function test_updating_user_role_revokes_his_tokens()
    {
        $user = $this->createAdminUser(store_token: true);

        // assert user has one access token
        $this->assertSame(1, $user->tokens->count());

        $user->staffEmail->update(['role_id' => 2]);
        // updates user's email and role_id
        $updated = $this->userService->update($user, role_id: 2);
        // get a fresh instance so tokens relationship will be updated
        $updated = $updated->fresh();
        // expects user's tokens have been deleted. 
        $this->assertSame(0, $updated->tokens->count());
    }

    public function test_user_can_delete_his_account()
    {
        $user = $this->createDentistUser();
        $this->userService->deleteUser($user, $user);
        $this->assertModelMissing($user);
    }
    public function test_deleting_user_throws_exception_if_not_by_admin_or_himself()
    {
        $this->assertThrows(function () {
            $user1 = $this->createDentistUser();
            $user2 = $this->createSecretaryUser();
            $this->userService->deleteUser($user1, $user2);
        }, UnauthorizedToDeleteUserException::class);
    }
    public function test_admin_can_delete_other_users()
    {
        $admin = $this->createAdminUser(grant_token: true);
        $user = $this->createSecretaryUser();

        $this->userService->deleteUser($user, $admin);
        // expect $user to be deleted from db
        $this->assertModelMissing($user);
    }

    public function test_deleting_user_will_delete_his_tokens()
    {
        $user = $this->createDentistUser(store_token: true);
        $this->userService->deleteUser($user, $user);
        $this->assertDatabaseMissing('personal_access_tokens', ['tokenable_id' => $user->id]);
    }
}
