<?php

namespace Tests\Unit;

use Illuminate\Http\JsonResponse;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RoleSeeder;
use Database\Seeders\StaffEmailSeeder;
use Illuminate\Database\Seeder;
use App\Services\UserService;
use App\Traits\ProvidesApiJsonResponse;
use Database\Seeders\Utils\ProvidesUserSeedingData;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            StaffEmailSeeder::class,
        ]);
    }
}

class UserServiceTest extends TestCase
{
    use  RefreshDatabase, ProvidesApiJsonResponse, ProvidesUserSeedingData;
    private UserService $userService;
    private $valid_user_credentials = [
        'email' => 'admin@dentalClinic.com',
        'name' => 'admin1',
        'password' => 'clinic123',
    ];

    protected $seed = true;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    protected $seeder = DatabaseSeeder::class;
    protected function setUp(): void
    {
        $this->userService = new UserService();
        parent::setUp();
    }
    public function test_user_creation_with_valid_credentials()
    {
        $response =   $this->userService->createNewUser(
            $this->valid_user_credentials['email'],
            $this->valid_user_credentials['name'],
            $this->valid_user_credentials['password']
        );
        var_dump($response->content());
        $this->asset;
        $this->assertEquals($response->status(), JsonResponse::HTTP_CREATED);
    }
}
