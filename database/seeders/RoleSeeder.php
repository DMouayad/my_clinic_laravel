<?php

namespace Database\Seeders;

use Domain\Users\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('roles')->truncate();
        Schema::enableForeignKeyConstraints();

        $roles = [
            ['name' => 'Administrator', 'slug' => 'admin'],
            ['name' => 'Dentist', 'slug' => 'dentist'],
            ['name' => 'Secretary', 'slug' => 'secretary'],
            ['name' => 'Patient', 'slug' => 'patient'],
        ];

        collect($roles)->each(function ($role) {
            Role::create($role);
        });
    }
}
