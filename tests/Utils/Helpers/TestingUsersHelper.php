<?php

namespace Tests\Utils\Helpers;

use App\Models\StaffMember;
use App\Models\User;
use App\Services\UserService;
use Database\Seeders\Utils\ProvidesUserSeedingData;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\Utils\Enums\UserRole;

class TestingUsersHelper
{
    use ProvidesUserSeedingData;

    public function __construct(private readonly UserService $userService)
    {
    }

    /**
     * @param \App\Models\User $user
     * @return void
     */
    public function assignStaffMemberUser(User $user): void
    {
        StaffMember::where("email", $user->email)->update([
            "user_id" => $user->id,
        ]);
    }

    /**
     *
     * @param \Tests\Utils\Enums\UserRole $userRole
     * @param boolean $grant_access_token
     * @param boolean $store_access_token
     * @param bool $store_refresh_token
     * @return \App\Models\User
     * @throws \App\Exceptions\EmailAlreadyRegisteredException
     * @throws \App\Exceptions\PhoneNumberAlreadyUsedException
     */
    public function createUserByRole(
        UserRole $userRole,
        bool $grant_access_token = false,
        bool $store_access_token = false,
        bool $store_refresh_token = false
    ): User {
        $role_slug = $userRole->value;
        $user = $this->userService->createNewUser(
            email: $this->users_seeding_emails[$role_slug],
            name: "test " . $role_slug,
            phone_number: Str::random(9),
            password: "password"
        );
        if ($grant_access_token) {
            $this->giveToken($user, [$role_slug]);
        }
        if ($store_access_token) {
            $this->createAccessToken($user, [$role_slug]);
        }
        if ($store_refresh_token) {
            $this->createRefreshToken($user);
        }
        return $user;
    }

    /**
     * creates a token for user which will work with tokenCan
     * @param \App\Models\User $user
     * @param array $abilities
     * @return void
     */
    private function giveToken(User &$user, array $abilities = [])
    {
        Sanctum::actingAs($user, $abilities);
    }

    /**
     * Creates a token for user and store it in the db.
     *
     * @param \App\Models\User $user
     * @param array $abilities
     * @return void
     */
    private function createAccessToken(User $user, array $abilities = [])
    {
        $user->createToken("test_token", $abilities);
    }

    private function createRefreshToken(User $user)
    {
        $user->createRefreshToken("test_refresh_token");
    }
}
