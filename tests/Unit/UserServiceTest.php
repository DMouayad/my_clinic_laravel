<?php

namespace Tests\Unit;

use App\Exceptions\EmailAlreadyRegisteredException;
use App\Exceptions\PhoneNumberAlreadyUsedException;
use App\Exceptions\UnauthorizedToDeleteUserException;
use App\Exceptions\UserDoesntMatchHisStaffEmailException;
use App\Services\UserService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Utils\CustomDatabaseSeeders\RolesAndStaffEmailDBSeeder;
use Tests\Utils\Enums\UserRole;
use Tests\Utils\Helpers\TestingUsersHelper;

class UserServiceTest extends TestCase
{
    // it's important to execute db migrations before each test to ensure it's not
    // affected by other tests.
    use DatabaseMigrations;

    protected bool $seed = true;
    protected string $seeder = RolesAndStaffEmailDBSeeder::class;
    private TestingUsersHelper $helper;
    private UserService $userService;

    public function test_create_user_with_valid_credentials()
    {
        $user = $this->helper->createUserByRole(UserRole::admin);
        $this->assertModelExists($user);
    }

    public function test_creating_user_creates_staff_email_user()
    {
        $user = $this->helper->createUserByRole(UserRole::admin);
        $this->assertDatabaseHas("staff_email_user", [
            "user_id" => $user->id,
            "staff_email_id" => $user->staffEmail->id,
        ]);
    }

    public function test_creating_user_with_existing_email_throws_exception()
    {
        $this->assertThrows(function () {
            $this->helper->createUserByRole(UserRole::admin);
            $this->helper->createUserByRole(UserRole::admin);
        }, EmailAlreadyRegisteredException::class);
    }

    public function test_updating_user_data()
    {
        $user = $this->helper->createUserByRole(UserRole::admin);
        $new_email = "updatedEmail@myclinic.com";
        $new_phone_number = "newPhoneNumber";
        // user's staffEmail must be updated before updating the user, otherwise,
        // UserDoesntMatchHisStaffEmailException will be thrown
        $user->staffEmail->update([
            "email" => $new_email,
            "role_id" => 2,
            "phone_number" => $new_phone_number,
        ]);
        $updated = $this->userService->update(
            $user,
            2,
            $new_email,
            $new_phone_number
        );
        // assert user date was updated in the db
        $this->assertDatabaseHas("users", [
            "id" => $updated->id,
            "role_id" => $updated->role_id,
            "email" => $new_email,
            "phone_number" => $new_phone_number,
        ]);
    }

    public function test_updating_user_phoneNo_with_one_already_used_throws_exception()
    {
        $this->assertThrows(function () {
            $user1 = $this->helper->createUserByRole(UserRole::dentist);
            $user2 = $this->helper->createUserByRole(UserRole::secretary);
            $this->userService->update(
                $user1,
                phone_number: $user2->phone_number
            );
        }, PhoneNumberAlreadyUsedException::class);
    }

    public function test_updating_user_with_role_different_from_his_staffEmail_throws_exception()
    {
        $this->assertThrows(function () {
            $new_role_id = 2;
            $user = $this->helper->createUserByRole(UserRole::admin);
            $this->userService->update($user, role_id: $new_role_id);
        }, UserDoesntMatchHisStaffEmailException::class);
    }

    public function test_updating_user_with_email_different_from_his_staffEmail_throws_exception()
    {
        $this->assertThrows(function () {
            $new_email = "newEmail@myclinic.com";
            $user = $this->helper->createUserByRole(UserRole::admin);
            $this->userService->update($user, email: $new_email);
        }, UserDoesntMatchHisStaffEmailException::class);
    }

    public function test_updating_user_email_throws_exception_if_already_exists()
    {
        $this->assertThrows(function () {
            $user1 = $this->helper->createUserByRole(UserRole::dentist);
            $user2 = $this->helper->createUserByRole(UserRole::secretary);
            $this->userService->update($user1, email: $user2->email);
        }, EmailAlreadyRegisteredException::class);
    }

    public function test_updating_user_email_revokes_his_tokens()
    {
        $new_email = "updatedEmail@myclinic.com";
        $user = $this->helper->createUserByRole(
            UserRole::admin,
            store_access_token: true
        );
        // assert user has one access token
        $this->assertSame(1, $user->tokens->count());

        $user->staffEmail->update(["email" => $new_email]);

        // updates user's email and role_id
        $updated = $this->userService->update($user, email: $new_email);
        // get a fresh instance so tokens relationship will be updated
        $updated = $updated->fresh();
        // expects user's tokens have been deleted.
        $this->assertSame(0, $updated->tokens->count());
    }

    public function test_updating_user_role_revokes_his_tokens()
    {
        $user = $this->helper->createUserByRole(
            UserRole::admin,
            store_access_token: true,
            store_refresh_token: true
        );

        // assert user has one access token
        $this->assertSame(1, $user->tokens->count());
        $this->assertSame(1, $user->refreshTokens->count());

        $user->staffEmail->update(["role_id" => 2]);
        // updates user's email and role_id
        $updated = $this->userService->update($user, role_id: 2);
        // get a fresh instance so tokens relationship will be updated
        $updated = $updated->fresh();
        // expects user's tokens have been deleted.
        $this->assertSame(0, $updated->tokens->count());
        $this->assertSame(0, $updated->refreshTokens->count());
    }

    public function test_user_can_delete_his_account()
    {
        $user = $this->helper->createUserByRole(UserRole::dentist);
        $this->userService->delete($user, $user);
        $this->assertModelMissing($user);
    }

    public function test_deleting_user_throws_exception_if_not_by_admin_or_himself()
    {
        $this->assertThrows(function () {
            $user1 = $this->helper->createUserByRole(UserRole::dentist);
            $user2 = $this->helper->createUserByRole(UserRole::secretary);
            $this->userService->delete($user1, $user2);
        }, UnauthorizedToDeleteUserException::class);
    }

    public function test_admin_can_delete_other_users()
    {
        $admin = $this->helper->createUserByRole(
            UserRole::admin,
            grant_access_token: true
        );
        $user = $this->helper->createUserByRole(UserRole::secretary);

        $this->userService->delete($user, $admin);
        // expect $user to be deleted from db
        $this->assertModelMissing($user);
    }

    public function test_deleting_user_will_delete_his_tokens()
    {
        $user = $this->helper->createUserByRole(
            UserRole::admin,
            store_access_token: true,
            store_refresh_token: true
        );
        // assert tokens exists before deleting the user
        $this->assertDatabaseHas("personal_access_tokens", [
            "tokenable_id" => $user->id,
        ]);
        $this->assertDatabaseHas("refresh_tokens", [
            "tokenable_id" => $user->id,
        ]);
        // perform delete
        $this->userService->delete($user, $user);
        // assert both tokens were deleted
        $this->assertDatabaseMissing("personal_access_tokens", [
            "tokenable_id" => $user->id,
        ]);
        $this->assertDatabaseMissing("refresh_tokens", [
            "tokenable_id" => $user->id,
        ]);
    }

    protected function setUp(): void
    {
        $this->userService = new UserService();
        $this->helper = new TestingUsersHelper($this->userService);
        parent::setUp();
    }
}
