<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class StaffEmail extends Model
{
    protected $fillable = ['email', 'role_id'];

    protected $hidden = [
        'created_at',
        'updated_at',
        'role_id',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    /**
     * get the user which have registered using the same email address.
     */
    public function user()
    {
        return $this->hasOneThrough(User::class, StaffEmailUser::class, 'staff_email_id', 'id', 'id', 'user_id');
    }
}
