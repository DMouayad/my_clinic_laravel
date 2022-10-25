<?php

namespace Tests\Unit\UserPreferences;

use App\Exceptions\DeleteAttemptOfNonExistingModelException;
use Database\Seeders\DatabaseSeeder;
use Domain\UserPreferences\Actions\DeleteUserPreferencesAction;
use Domain\UserPreferences\Models\UserPreferences;
use Tests\Utils\CustomTestCases\BaseActionTestCaseWithRefreshDatabase;

class DeleteUserPreferencesActionTest extends
    BaseActionTestCaseWithRefreshDatabase
{
    public function getSeederClass(): string
    {
        return DatabaseSeeder::class;
    }

    public function action(): DeleteUserPreferencesAction
    {
        return app(DeleteUserPreferencesAction::class);
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
        $result = $this->action()->execute($this->getInstance());
        $this->assertTrue($result);
    }

    public function test_execution_with_invalid_data_is_failure(): void
    {
        $this->expectException(DeleteAttemptOfNonExistingModelException::class);
        $this->action()->execute(null);
    }
}
