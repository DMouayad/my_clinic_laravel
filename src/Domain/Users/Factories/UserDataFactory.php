<?php

namespace Domain\Users\Factories;

use Domain\StaffMembers\Models\StaffMember;
use Domain\Users\DataTransferObjects\CreateUserData;
use Domain\Users\DataTransferObjects\UpdateUserData;
use Domain\Users\Models\Role;
use Support\Factories\BaseFactory;

class UserDataFactory extends BaseFactory
{
    private ?string $email = null,
        $role_slug = null,
        $name = null,
        $phone_number = null,
        $password = null;

    public static function new(): self
    {
        return new self();
    }

    public function create(): CreateUserData
    {
        $this->email ??= $this->faker()->email();
        $this->role_slug ??= $this->faker()->randomElement([
            "admin",
            "secretary",
            "dentist",
            "patient",
        ]);
        $this->name ??= $this->faker()->name();
        $this->password ??= $this->faker()->password(minLength: 8);
        $this->phone_number ??= $this->faker()->phoneNumber();

        return new CreateUserData(
            email: $this->email,
            name: $this->name,
            phone_number: $this->phone_number,
            password: $this->password,
            role_id: Role::getIdWhereSlug($this->role_slug)
        );
    }

    public function createWithNullAttributes(): CreateUserData
    {
        return new CreateUserData(...null);
    }

    public function forUpdate(): UpdateUserData
    {
        return new UpdateUserData(
            name: $this->name,
            email: $this->email,
            phone_number: $this->phone_number,
            role_id: $this->role_slug
                ? Role::getIdWhereSlug($this->role_slug)
                : null
        );
    }

    public function fromExistingStaffMember(string $role): self
    {
        /**
         * @var StaffMember $staff_member
         */
        $staff_member = StaffMember::query()
            ->where("role_id", Role::getIdWhereSlug($role))
            ->first();
        return $this->fromStaffMember($staff_member);
    }

    public function fromStaffMember(StaffMember $staff_member): self
    {
        $clone = clone $this;
        $clone->email = $staff_member->email;
        $clone->role_slug = $staff_member->roleSlug();

        return $clone;
    }

    public function fromRandomStaffMember(): self
    {
        /**
         * @var StaffMember $staff_member
         */
        $staff_member = StaffMember::query()->first();
        return $this->fromStaffMember($staff_member);
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

    public function withSecretaryRole(): self
    {
        $clone = clone $this;
        $clone->role_slug = "secretary";

        return $clone;
    }
}
