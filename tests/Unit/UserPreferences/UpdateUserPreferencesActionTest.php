<?php

namespace Tests\Unit\UserPreferences;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Domain\UserPreferences\Actions\UpdateUserPreferencesAction;
use Domain\UserPreferences\Factories\UserPreferencesDataFactory;
use Domain\UserPreferences\Models\UserPreferences;
use Domain\Users\Exceptions\UserNotFoundException;
use Tests\Utils\CustomTestCases\BaseActionTestCaseWithRefreshDatabase;
use TypeError;

class UpdateUserPreferencesActionTest extends
    BaseActionTestCaseWithRefreshDatabase
{
    public function getSeederClass(): string
    {
        return DatabaseSeeder::class;
    }

    public function test_update_user_preferences_with_nonExisting_userId_throws_exception()
    {
        $this->expectException(UserNotFoundException::class);

        $nonExistingUserId = User::query()->count() + 1;
        // assert user with the id doesn't exist
        $this->assertDatabaseMissing("users", ["id" => $nonExistingUserId]);
        $this->action()->execute(
            $this->getInstance(),
            data: UserPreferencesDataFactory::new()
                ->withUserId($nonExistingUserId)
                ->create()
        );
    }

    public function action(): UpdateUserPreferencesAction
    {
        return app(UpdateUserPreferencesAction::class);
    }

    public function getInstance(): UserPreferences
    {
        /**
         * @var UserPreferences $user_preferences
         */
        $user_preferences = UserPreferences::query()->first();
        return $user_preferences;
    }

    public function test_execution_with_valid_data_is_success(): void
    {
        $result = $this->action()->execute(
            $this->getInstance(),
            data: UserPreferencesDataFactory::new()->create()
        );
        $this->assertTrue($result);
    }

    public function test_execution_with_invalid_data_is_failure(): void
    {
        $this->expectException(TypeError::class);
        $this->action()->execute(
            $this->getInstance(),
            data: UserPreferencesDataFactory::new()->createWithNullAttributes()
        );
    }

    public function test_updating_non_existing_user_preferences_creates_new_one()
    {
        /**
         * @var User $user
         */
        $user = User::query()->first(["id"]);
        $data = UserPreferencesDataFactory::new()
            ->withUserId($user->id)
            ->create();
        if (!is_null($user->preferences)) {
            $user->preferences()->delete();
            $user = $user->refresh();
        }

        $this->assertNull($user->preferences);
        // act
        $was_created = $this->action()->execute(null, data: $data);
        // assert
        $this->assertTrue($was_created);
        $this->assertDatabaseHas("user_preferences", [
            "user_id" => $user->id,
            "theme" => $data->theme,
            "locale" => $data->locale,
        ]);
    }
}
