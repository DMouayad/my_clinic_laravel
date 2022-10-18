<?php

namespace Database\Seeders;

use Database\Seeders\Utils\ProvidesUserSeedingData;
use Database\Seeders\Utils\UserSeedingData;
use Domain\StaffMembers\Models\StaffMember;
use Domain\Users\Models\Role;
use Illuminate\Database\Seeder;

class StaffMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        StaffMember::factory(500)->create();

        foreach (UserSeedingData::emails as $role_slug => $email_address) {
            StaffMember::create([
                "email" => $email_address,
                "role_id" => Role::getIdWhereSlug($role_slug),
            ]);
        }
    }
}
