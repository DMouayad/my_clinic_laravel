<?php

namespace Tests\Unit\UserPreferences;

use App\Models\User;
use Domain\UserPreferences\Actions\CreateUserPreferencesAction;
use Domain\UserPreferences\Factories\UserPreferencesDataFactory;
use Domain\Users\Exceptions\UserNotFoundException;
use Tests\Utils\CustomDatabaseSeeders\AllExceptUserPreferencesDBSeeder;
use Tests\Utils\CustomTestCases\BaseActionTestCaseWithMigrations;
use TypeError;

class CreateUserPreferencesActionTest extends BaseActionTestCaseWithMigrations
{
    public function getSeederClass(): string
    {
        return AllExceptUserPreferencesDBSeeder::class;
    }

    public function test_create_user_preferences_with_nonExisting_userId_throws_exception()
    {
        $this->expectException(UserNotFoundException::class);

        $nonExistingUserId = User::query()->count() + 1;
        // assert user with the id doesn't exist
        $this->assertDatabaseMissing("users", ["id" => $nonExistingUserId]);

        $this->action()->execute(
            UserPreferencesDataFactory::new()
                ->withUserId($nonExistingUserId)
                ->create()
        );
    }

    public function action(): CreateUserPreferencesAction
    {
        return app(CreateUserPreferencesAction::class);
    }

    public function test_execution_with_valid_data_is_success(): void
    {
        $prefs = $this->action()->execute(
            UserPreferencesDataFactory::new()->create()
        );
        $this->assertModelExists($prefs);
    }

    public function test_execution_with_invalid_data_is_failure(): void
    {
        $this->expectException(TypeError::class);
        $this->action()->execute(
            UserPreferencesDataFactory::new()->createWithNullAttributes()
        );
    }
}
