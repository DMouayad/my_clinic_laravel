<?php

namespace Tests\Utils\Helpers;

use App\Models\User;
use App\Services\UserService;
use Database\Seeders\Utils\ProvidesUserSeedingData;
use Laravel\Sanctum\Sanctum;

class TestingUsersHelper
{
    use ProvidesUserSeedingData;

    public function __construct(private UserService $userService)
    {
    }

    /**
     *
     * @param \Tests\Utils\Helpers\UserRole $role
     * @param boolean $grant_access_token
     * @param boolean $store_access_token
     * @return \App\Models\User
     */
    public function createUserByRole(
        UserRole $userRole,
        bool $grant_access_token = false,
        bool $store_access_token = false,
        bool $store_refresh_token = false
    ): User {
        $role_slug = $userRole->value;
        $user = $this->userService->createNewUser(
            $this->users_seeding_emails[$role_slug],
            "test " . $role_slug,
            "password"
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

enum UserRole: string
{
    case admin = "admin";
    case dentist = "dentist";
    case secretary = "secretary";
    case patient = "patient";
}
