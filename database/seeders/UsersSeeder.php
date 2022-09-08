<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\StaffEmail;
use App\Models\StaffEmailUser;
use App\Models\User;
use Database\Seeders\Utils\ProvidesUserSeedingData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;


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


        $admin = User::create([
            'email' => $this->users_seeding_emails['admin'],
            'password' => Hash::make($this->default_password),
            'name' => 'admin1',
            'role_id' => Role::getIdBySlug('admin'),
            'email_verified_at' => now(),
        ]);

        $dentist = User::create([
            'email' => $this->users_seeding_emails['dentist'],
            'password' => Hash::make($this->default_password),
            'name' => 'dentist1',
            'role_id' => Role::getIdBySlug('dentist'),
            'email_verified_at' => now(),
        ]);

        $secretary = User::create([
            'email' => $this->users_seeding_emails['secretary'],
            'password' => Hash::make($this->default_password),
            'name' => 'secretary1',
            'role_id' => Role::getIdBySlug('secretary'),
            'email_verified_at' => now(),
        ]);
        StaffEmailUser::create([
            'user_id' => $admin->id,
            'staff_email_id' => StaffEmail::whereEmail($admin->email)->first()->id,
        ]);
        StaffEmailUser::create([
            'user_id' => $dentist->id,
            'staff_email_id' => StaffEmail::whereEmail($dentist->email)->first()->id,
        ]);
        StaffEmailUser::create([
            'user_id' => $secretary->id,
            'staff_email_id' => StaffEmail::whereEmail($secretary->email)->first()->id,
        ]);
    }
}
