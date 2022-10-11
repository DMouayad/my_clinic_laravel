<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\StaffMember;
use Database\Seeders\Utils\ProvidesUserSeedingData;
use Illuminate\Database\Seeder;

class StaffMemberSeeder extends Seeder
{
    use ProvidesUserSeedingData;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->users_seeding_emails as $role_slug => $email_address) {
            StaffMember::create([
                "email" => $email_address,
                "role_id" => Role::getIdBySlug($role_slug),
            ]);
        }
    }
}
