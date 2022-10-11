<?php

namespace Tests\Utils\CustomDatabaseSeeders;

use Database\Seeders\RoleSeeder;
use Database\Seeders\StaffMemberSeeder;
use Illuminate\Database\Seeder;

class RolesAndStaffMemberSeeder extends Seeder
{
    public function run()
    {
        $this->call([RoleSeeder::class, StaffMemberSeeder::class]);
    }
}
