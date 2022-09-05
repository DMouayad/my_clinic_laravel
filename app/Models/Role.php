<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\RoleNotFoundException;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }


    /**
     * Get Role by role slug.
     * @param string $slug
     * @return \App\Models\Role|null
     **/
    public static function findBySlug(string $slug)
    {
        return self::firstWhere('slug', $slug);
    }
    /**
     * Get Role ID by role slug.
     * @param string $slug
     * @return int|null
     * @throws \App\Exceptions\RoleNotFoundException
     **/
    public static function getIdBySlug(string $slug)
    {
        try {
            return self::where('slug', $slug)->firstOrFail(['id'])->id;
        } catch (ModelNotFoundException $e) {
            throw new RoleNotFoundException($slug);
        }
    }
}
