<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\StaffEmail;
use Illuminate\Database\Seeder;
use Database\Seeders\Utils\ProvidesUserSeedingData;


class StaffEmailSeeder extends Seeder
{
    use ProvidesUserSeedingData;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->users_emails as $role_slug => $email_address) {
            StaffEmail::create(
                [
                    'email' => $email_address,
                    'role_id' => Role::getIdBySlug($role_slug),
                ]
            );
        }
    }
}
