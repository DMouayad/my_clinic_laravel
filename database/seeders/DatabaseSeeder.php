<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use This;

class DatabaseSeeder extends Seeder
{
    private $seeders = [];

    public static function all()
    {
        $clonse = self::class;
    }

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            StaffMemberSeeder::class,
            UsersSeeder::class,
            UserPreferencesSeeder::class,
        ]);
    }
}
