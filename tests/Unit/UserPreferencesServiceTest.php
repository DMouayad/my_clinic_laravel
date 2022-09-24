<?php

namespace Tests\Unit;

use App\Exceptions\UserNotFoundException;
use App\Models\User;
use App\Services\UserPreferencesService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Utils\CustomDatabaseSeeders\AllExceptUserPreferencesDBSeeder;
use TypeError;

class UserPreferencesServiceTest extends TestCase
{
    // it's important to execute db migrations before each test to ensure it's not
    // affected by other tests.
    use DatabaseMigrations;

    protected bool $seed = true;
    protected string $seeder = AllExceptUserPreferencesDBSeeder::class;
    private UserPreferencesService $userPreferencesService;
    private array $data = ["theme" => "dark", "locale" => "en"];

    public function setUp(): void
    {
        $this->userPreferencesService = new UserPreferencesService();
        parent::setUp();
    }

    public function test_store_new_user_preferences_with_valid_data()
    {
        $user = User::find(1);
        $user_preferences = $this->userPreferencesService->store(
            $user->id,
            ...$this->data
        );
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
            // setup

            $notExistingUserId = config("my_clinic.seeded_users_count") + 1;
            // assert user with id doesn't exist
            $this->assertDatabaseMissing("users", ["id" => $notExistingUserId]);

            $this->userPreferencesService->store(
                $notExistingUserId,
                ...$this->data
            );
        }, UserNotFoundException::class);
    }

    public function test_update_user_preferences_with_valid_data_is_completed_successfully()
    {
        $user = User::find(1);
        $user_preferences = $this->userPreferencesService->store(
            $user->id,
            ...$this->data
        );
        $this->assertModelExists($user_preferences);

        $updated = $this->userPreferencesService->update(
            $user_preferences,
            "new_theme",
            "new_locale"
        );
        $this->assertTrue($updated);
        $this->assertDatabaseHas("user_preferences", [
            "user_id" => $user->id,
            "theme" => "new_theme",
            "locale" => "new_locale",
        ]);
    }

    public function test_delete_user_preferences_is_completed_successfully()
    {
        // create new UserPreferences
        $user = User::find(1);
        $user_preferences = $this->userPreferencesService->store(
            $user->id,
            ...$this->data
        );
        $this->assertModelExists($user_preferences);
        // perform delete op
        $this->userPreferencesService->delete($user_preferences);
        $this->assertModelMissing($user_preferences);
    }
}
