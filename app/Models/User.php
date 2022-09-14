<?php

namespace App\Models;

use App\Exceptions\UserNotFoundException;
use App\Exceptions\UserPreferencesAlreadyExistsException;
use App\Notifications\SendQueuedEmailVerificationNotification;
use DDragon\SanctumRefreshToken\HasRefreshTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

//use DDragon\SanctumRefreshToken\HasRefreshTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRefreshTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ["name", "email", "password", "role_id"];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ["password", "remember_token", "role_id"];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        "email_verified_at" => "datetime",
    ];

    public static function checkIfExists(int $user_id)
    {
        try {
            User::findOrFail($user_id);
        } catch (ModelNotFoundException $e) {
            throw new UserNotFoundException($user_id);
        }
    }

    /**
     *
     * @param integer $user_id
     * @return void
     * @throws UserPreferencesAlreadyExistsException
     */
    public static function checkHasPreferences(int $user_id)
    {
        $has_prefs = User::whereId($user_id)
            ->first()
            ->preferences()
            ->exists();
        if ($has_prefs) {
            throw new UserPreferencesAlreadyExistsException($user_id);
        }
    }

    public function preferences(): HasOne
    {
        return $this->hasOne(UserPreferences::class);
    }

    public function roleSlug(): string
    {
        return $this->role()
            ->get(["slug"])
            ->first()->slug;
    }

    public function role()
    {
        return $this->belongsTo(Role::class, "role_id", "id");
    }

    public function staffEmail()
    {
        return $this->hasOneThrough(
            StaffEmail::class,
            StaffEmailUser::class,
            "user_id",
            "id",
            "id",
            "staff_email_id"
        );
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new SendQueuedEmailVerificationNotification());
    }
}
