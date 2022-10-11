<?php

namespace Tests\Utils\CustomDatabaseSeeders;

use Database\Seeders\RoleSeeder;
use Database\Seeders\StaffMemberSeeder;
use Database\Seeders\UsersSeeder;
use Illuminate\Database\Seeder;

class AllExceptUserPreferencesDBSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            StaffMemberSeeder::class,
            UsersSeeder::class,
        ]);
    }
}
