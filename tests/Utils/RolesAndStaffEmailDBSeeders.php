<?php

namespace Tests\Utils;

use Illuminate\Database\Seeder;
use Database\Seeders\StaffEmailSeeder;
use Database\Seeders\RoleSeeder;

class RolesAndStaffEmailDBSeeders extends Seeder
{
    public function run()
    {
        $this->call([RoleSeeder::class, StaffEmailSeeder::class]);
    }
}
