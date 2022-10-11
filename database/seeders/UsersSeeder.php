<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\StaffMember;
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
        DB::table("users")->truncate();
        Schema::enableForeignKeyConstraints();

        $admin = User::create([
            "email" => $this->users_seeding_emails["admin"],
            "password" => Hash::make($this->default_password),
            "name" => "admin1",
            "phone_number" => $this->getRandomPhoneNum(),
            "role_id" => Role::getIdBySlug("admin"),
            "email_verified_at" => now(),
        ]);
        StaffMember::where("email", $admin->email)->update([
            "user_id" => $admin->id,
        ]);
        $dentist = User::create([
            "email" => $this->users_seeding_emails["dentist"],
            "password" => Hash::make($this->default_password),
            "name" => "dentist1",
            "phone_number" => $this->getRandomPhoneNum(),
            "role_id" => Role::getIdBySlug("dentist"),
            "email_verified_at" => now(),
        ]);
        StaffMember::where("email", $dentist->email)->update([
            "user_id" => $dentist->id,
        ]);

        $secretary = User::create([
            "email" => $this->users_seeding_emails["secretary"],
            "password" => Hash::make($this->default_password),
            "name" => "secretary1",
            "phone_number" => $this->getRandomPhoneNum(),
            "role_id" => Role::getIdBySlug("secretary"),
            "email_verified_at" => now(),
        ]);
        StaffMember::where("email", $secretary->email)->update([
            "user_id" => $secretary->id,
        ]);
    }
}
