<?php

    namespace Database\Seeders;

    use App\Models\User;
    use Database\Seeders\Utils\UserSeedingData;
    use Domain\StaffMembers\Models\StaffMember;
    use Domain\Users\Models\Role;
    use Illuminate\Database\Seeder;
    use Illuminate\Support\Facades\Hash;

    class AdminUserSeeder extends Seeder
    {

        public function run(){

            $staff_member = StaffMember::create([
                'email'   => 'myclinic.admin@myclinic.com',
                'role_id' => Role::getIdWhereSlug('admin'),

            ]);
            User::create([
                "email"        => $staff_member->email,
                "password"     => Hash::make(UserSeedingData::default_password),
                "name"         => "admin",
                "phone_number" => '0987654321',
                "role_id"      => $staff_member->role_id,
            ]);
        }
    }
