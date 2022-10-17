<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\Utils\ProvidesUserSeedingData;
use Database\Seeders\Utils\UserSeedingData;
use Domain\StaffMembers\Models\StaffMember;
use Domain\Users\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UsersSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table("users")->truncate();
        Schema::enableForeignKeyConstraints();

        $admin = User::create([
            "email" => UserSeedingData::emails["admin"],
            "password" => Hash::make(UserSeedingData::default_password),
            "name" => "admin1",
            "phone_number" => UserSeedingData::getRandomPhoneNum(),
            "role_id" => Role::getIdWhereSlug("admin"),
            "email_verified_at" => now(),
        ]);
        StaffMember::where("email", $admin->email)->update([
            "user_id" => $admin->id,
        ]);
        $dentist = User::create([
            "email" => UserSeedingData::emails["dentist"],
            "password" => Hash::make(UserSeedingData::default_password),
            "name" => "dentist1",
            "phone_number" => UserSeedingData::getRandomPhoneNum(),
            "role_id" => Role::getIdWhereSlug("dentist"),
            "email_verified_at" => now(),
        ]);
        StaffMember::where("email", $dentist->email)->update([
            "user_id" => $dentist->id,
        ]);

        $secretary = User::create([
            "email" => UserSeedingData::emails["secretary"],
            "password" => Hash::make(UserSeedingData::default_password),
            "name" => "secretary1",
            "phone_number" => UserSeedingData::getRandomPhoneNum(),
            "role_id" => Role::getIdWhereSlug("secretary"),
            "email_verified_at" => now(),
        ]);
        StaffMember::where("email", $secretary->email)->update([
            "user_id" => $secretary->id,
        ]);
    }
}
