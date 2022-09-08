<?php

namespace Database\Seeders;

use App\Models\UserPreferences;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use  App\Models\User;

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
                'user_id' => $user->id,
                'theme' => 'dark',
                'language' => 'en',
            ]);
        }
    }
}
