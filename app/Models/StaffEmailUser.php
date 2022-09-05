<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffEmailUser extends Model
{

    use HasFactory;

    protected $table = 'staff_email_user';
    protected $fillable = [
        'staff_email_id',
        'user_id'
    ];
    public function user()
    {
        return $this->hasOne(User::class, 'user_id');
    }
    public function staffEmail()
    {
        return $this->hasOne(StaffEmail::class, 'staff_email_id');
    }
}
