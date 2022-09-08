<?php

namespace Tests\Unit;

use App\Exceptions\UserNotFoundException;
use App\Models\User;
use App\Services\UserPreferencesService;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\StaffEmailSeeder;
use Database\Seeders\UsersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use TypeError;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            StaffEmailSeeder::class,
            UsersSeeder::class,
        ]);
    }
}
class UserPreferencesServiceTest extends TestCase
{
    use RefreshDatabase;
    protected $seed = true;
    protected $seeder = DatabaseSeeder::class;
    private UserPreferencesService $userPreferencesService;
    private $data = ['theme' => 'dark', 'language' => 'en'];
    public function setUp(): void
    {
        $this->userPreferencesService = new UserPreferencesService();
        parent::setUp();
    }

    public function test_store_new_user_preferences_with_valid_data()
    {
        $user = User::find(1);
        $user_preferences = $this->userPreferencesService->store($user->id, ...$this->data);
        $this->assertModelExists($user_preferences);
    }

    public function test_store_new_user_preferences_with_missing_userId()
    {
        $this->assertThrows(function () {
            $this->userPreferencesService->store(null, ...$this->data);
        }, TypeError::class);
    }
    public function test_store_new_user_preferences_with_nonExisting_userId_throws_exception()
    {
        $this->assertThrows(function () {
            $this->userPreferencesService->store(5, ...$this->data);
        }, UserNotFoundException::class);
    }
    public function test_update_user_preferences()
    {
        $user = User::find(1);
        $user_preferences = $this->userPreferencesService->store($user->id, ...$this->data);
        $this->assertModelExists($user_preferences);

        $updated = $this->userPreferencesService->update($user_preferences, 'new_theme', 'new_language');
        $this->assertTrue($updated);
        $this->assertDatabaseHas(
            'user_preferences',
            ['user_id' => $user->id, 'theme' => 'new_theme', 'language' => 'new_language']
        );
    }
    public function test_delete_user_preferences()
    {
        // create new UserPreferences
        $user = User::find(1);
        $user_preferences = $this->userPreferencesService->store($user->id, ...$this->data);
        $this->assertModelExists($user_preferences);
        // perform delete op
        $this->userPreferencesService->delete($user_preferences);
        $this->assertModelMissing($user_preferences);
    }
}
