<?php

namespace Domain\Users\Models;

use App\Models\User;
use Domain\Users\Exceptions\RoleNotFoundException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ["name", "slug"];

    protected $hidden = ["id", "created_at", "updated_at"];

    /**
     * Get Role ID by role slug.
     *
     * @param string $slug
     * @return int|null
     * @throws \Domain\Users\Exceptions\RoleNotFoundException
     **/
    public static function getIdWhereSlug(string $slug): ?int
    {
        try {
            return self::where("slug", $slug)->firstOrFail(["id"])->id;
        } catch (ModelNotFoundException $e) {
            throw new RoleNotFoundException($slug);
        }
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
