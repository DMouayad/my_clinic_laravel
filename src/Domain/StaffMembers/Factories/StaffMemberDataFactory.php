<?php

namespace Domain\StaffMembers\Factories;

use Domain\StaffMembers\DataTransferObjects\StaffMemberData;
use Support\Factories\BaseFactory;

class StaffMemberDataFactory extends BaseFactory
{
    private ?string $email = null,
        $role_slug = null;

    public static function new(): self
    {
        return new self();
    }

    public function forUpdate(): StaffMemberData
    {
        return StaffMemberData::forUpdate($this?->email, $this?->role_slug);
    }

    public function withEmail(?string $email): self
    {
        $clone = clone $this;
        $clone->email = $email;

        return $clone;
    }

    public function withAdminRole(): self
    {
        return $this->withRole("admin");
    }

    public function withRole(?string $role_slug): self
    {
        $clone = clone $this;
        $clone->role_slug = $role_slug;

        return $clone;
    }

    public function withDentistRole(): self
    {
        return $this->withRole("dentist");
    }

    public function withSecretaryRole(): self
    {
        return $this->withRole("secretary");
    }

    public function createWithNullAttributes(): StaffMemberData
    {
        return StaffMemberData::forCreate(null, null);
    }

    public function forCreate(): StaffMemberData
    {
        $this->email ??= $this->faker()->email();
        $this->role_slug ??= $this->faker()->randomElement([
            "admin",
            "secretary",
            "dentist",
            "patient",
        ]);
        return StaffMemberData::forCreate($this->email, $this->role_slug);
    }

    public function create(): StaffMemberData
    {
        return $this->forCreate();
    }
}
