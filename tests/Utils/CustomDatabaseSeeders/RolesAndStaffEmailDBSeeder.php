<?php

namespace Tests\Utils\CustomDatabaseSeeders;

use Database\Seeders\RoleSeeder;
use Database\Seeders\StaffEmailSeeder;
use Illuminate\Database\Seeder;

class RolesAndStaffEmailDBSeeder extends Seeder
{
    public function run()
    {
        $this->call([RoleSeeder::class, StaffEmailSeeder::class]);
    }
}
