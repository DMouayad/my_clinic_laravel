<?php

namespace Tests\Utils\CustomDatabaseSeeders;

use Database\Seeders\RoleSeeder;
use Database\Seeders\StaffEmailSeeder;
use Database\Seeders\UsersSeeder;
use Illuminate\Database\Seeder;

class AllExceptUserPreferencesDBSeeder extends Seeder
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
