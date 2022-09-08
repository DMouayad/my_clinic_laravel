<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\StaffEmail;
use App\Models\StaffEmailUser;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\SendQueuedEmailVerificationNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'role_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function roleSlug(): string
    {
        return $this->role()->get(['slug'])->first()->slug;
    }

    public function staffEmail()
    {
        return $this->hasOneThrough(
            StaffEmail::class,
            StaffEmailUser::class,
            'user_id',
            'id',
            'id',
            'staff_email_id'
        );
    }
    public function sendEmailVerificationNotification()
    {
        $this->notify(new SendQueuedEmailVerificationNotification());
    }

    public function userPreferences()
    {
        return $this->hasOne(UserPreferences::class);
    }
}
