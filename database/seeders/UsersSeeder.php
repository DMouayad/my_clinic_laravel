<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Database\Seeders\Utils\ProvidesUserSeedingData;


class UsersSeeder extends Seeder
{
    use ProvidesUserSeedingData;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();
        Schema::enableForeignKeyConstraints();


        User::create([
            'email' => $this->users_emails['admin'],
            'password' => Hash::make($this->default_password),
            'name' => 'admin1',
            'role_id' => Role::getIdBySlug('admin'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'email' => $this->users_emails['dentist'],
            'password' => Hash::make($this->default_password),
            'name' => 'dentist1',
            'role_id' => Role::getIdBySlug('dentist'),
        ]);

        User::create([
            'email' => $this->users_emails['secretary'],
            'password' => Hash::make($this->default_password),
            'name' => 'secretary1',
            'role_id' => Role::getIdBySlug('secretary'),

        ]);
    }
}
