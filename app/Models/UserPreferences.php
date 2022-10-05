<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents user app's theme and language preferences
 */
class UserPreferences extends Model
{
    use HasFactory;

    protected $fillable = [
        'theme',
        'locale',
        'user_id',
    ];
    protected $hidden = ['id', 'user_id', 'created_at', 'updated_at'];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'user_id';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
