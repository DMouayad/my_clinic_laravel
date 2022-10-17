<?php

namespace Database\Seeders;

use App\Models\User;
use Domain\UserPreferences\Models\UserPreferences;
use Illuminate\Database\Seeder;

class UserPreferencesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        foreach ($users as $user) {
            UserPreferences::create([
                "user_id" => $user->id,
                "theme" => "dark",
                "locale" => "en",
            ]);
        }
    }
}
