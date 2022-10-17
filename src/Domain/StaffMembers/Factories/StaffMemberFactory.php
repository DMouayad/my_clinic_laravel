<?php

namespace Domain\StaffMembers\Factories;

use Domain\StaffMembers\Models\StaffMember;
use Illuminate\Database\Eloquent\Factories\Factory;

class StaffMemberFactory extends Factory
{
    protected $model = StaffMember::class;

    public function definition()
    {
        return [
            "email" => fake()->email(),
            "role_id" => fake()->numberBetween(1, 3),
        ];
    }
}
