<?php

namespace Tests\Utils\CustomDatabaseSeeders;

use Database\Seeders\RoleSeeder;
use Database\Seeders\UsersSeeder;
use Illuminate\Database\Seeder;

class UsersAndRolesDBSeeder extends Seeder
{
    public function run()
    {
        $this->call([RoleSeeder::class, UsersSeeder::class]);
    }
}
