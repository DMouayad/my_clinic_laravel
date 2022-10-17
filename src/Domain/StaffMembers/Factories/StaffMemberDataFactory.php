<?php

namespace Domain\StaffMembers\Factories;

use Domain\StaffMembers\DataTransferObjects\StaffMemberData;
use Faker\Factory;
use Faker\Generator;
use Support\Factories\BaseFactory;

class StaffMemberDataFactory extends BaseFactory
{
    private Generator $faker;
    private string $email;
    private string $role_slug;

    public static function new(): self
    {
        return new self();
    }

    public function create(): StaffMemberData
    {
        $this->faker ??= Factory::create();
        $this->email ??= $this->faker->email();
        $this->role_slug ??= $this->faker->randomElement([
            "admin",
            "secretary",
            "dentist",
            "patient",
        ]);
        return StaffMemberData::forCreate($this->email, $this->role_slug);
    }

    public function withEmail(string $email): self
    {
        $clone = clone $this;
        $clone->email = $email;

        return $clone;
    }

    public function withRole(string $role_slug): self
    {
        $clone = clone $this;
        $clone->role_slug = $role_slug;

        return $clone;
    }

    public function withAdminRole(): self
    {
        $clone = clone $this;
        $clone->role_slug = "admin";

        return $clone;
    }

    public function withDentistRole(): self
    {
        $clone = clone $this;
        $clone->role_slug = "dentist";

        return $clone;
    }

    public function withSecretaryRole(): self
    {
        $clone = clone $this;
        $clone->role_slug = "secretary";

        return $clone;
    }

    public function nullAttributes(): self
    {
        $clone = clone $this;
        $clone->role_slug = null;
        $clone->email = null;
        return $clone;
    }
}
