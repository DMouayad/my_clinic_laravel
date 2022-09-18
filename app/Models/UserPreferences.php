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
    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'user_id';
    }
    protected $fillable = [
        'theme',
        'language',
        'user_id',
    ];
    protected $hidden = [ 'created_at', 'updated_at'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
