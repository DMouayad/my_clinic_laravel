<?php

namespace Domain\StaffMembers\Models;

use App\Models\User;
use Domain\StaffMembers\DataTransferObjects\StaffMemberData;
use Domain\StaffMembers\Factories\StaffMemberFactory;
use Domain\Users\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Represents a clinic's staff member.
 *
 * Users with a staff role (dentist-secretary-admin) can only register with an
 * already-saved StaffMember's email address.
 */
class StaffMember extends Model
{
    use HasFactory;

    protected $fillable = ["email", "role_id"];
    protected $hidden = ["created_at", "updated_at", "user_id", "role_id"];

    public static function findWhereEmail(string $email): self
    {
        $staff_member = StaffMember::where("email", $email)->first();
        if (!$staff_member) {
            throw new ModelNotFoundException(
                "StaffMember with email (" . $email . ") not found!"
            );
        }
        return $staff_member;
    }

    public static function findWhereRole(string $role): self
    {
        $staff_member = StaffMember::where(
            "role_id",
            Role::getIdWhereSlug($role)
        )->first();
        if (!$staff_member) {
            throw new ModelNotFoundException(
                "StaffMember with role (" . $role . ") not found!"
            );
        }
        return $staff_member;
    }

    public static function whereUserId(int $user_id): self
    {
        return StaffMember::where("user_id", $user_id)->first();
    }

    protected static function newFactory()
    {
        return StaffMemberFactory::new();
    }

    public function roleSlug(): string
    {
        return $this->role()
            ->get(["slug"])
            ->first()->slug;
    }

    public function role()
    {
        return $this->belongsTo(Role::class, "role_id");
    }

    public function updateFromData(StaffMemberData $data): self
    {
        $this->email = $data->email ?? $this->email;
        if ($data->role) {
            $this->role_id =
                Role::getIdWhereSlug($data->role) ?? $this->role_id;
        }
        $this->user_id = $data->user_id ?? $this->user_id;
        return $this;
    }

    /**
     * get the user which have registered using the same email address.
     */
    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
