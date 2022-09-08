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
     * @param boolean $grant_token
     * @param boolean $store_token
     * @return \App\Models\User
     */
    public function createAdminUser(bool $grant_token = false, bool $store_token = false): User
    {
        return $this->createUserByRole('admin', $grant_token, $store_token);
    }

    /**
     *
     * @param string $role
     * @param boolean $grant_token
     * @param boolean $store_token
     * @return \App\Models\User
     */
    private function createUserByRole(string $role, bool $grant_token = false, bool $store_token = false): User
    {
        $user = $this->userService->createNewUser(
            $this->users_seeding_emails[$role],
            'test ' . $role,
            'password'
        );
        if ($grant_token) {
            $this->giveToken($user, [$role]);
        }
        if ($store_token) {
            $this->createToken($user, [$role]);
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
    private function createToken(User $user, array $abilities = [])
    {
        $user->createToken('test_token', $abilities)->plainTextToken;
    }

    /**
     *
     * @param boolean $grant_token
     * @param boolean $store_token
     * @return \App\Models\User
     */
    public function createDentistUser(bool $grant_token = false, bool $store_token = false): User
    {
        return $this->createUserByRole('dentist', $grant_token, $store_token);
    }

    /**
     *
     * @param boolean $grant_token
     * @param boolean $store_token
     * @return User
     */
    public function createSecretaryUser(bool $grant_token = false, bool $store_token = false): User
    {
        return $this->createUserByRole('secretary', $grant_token, $store_token);
    }
}
