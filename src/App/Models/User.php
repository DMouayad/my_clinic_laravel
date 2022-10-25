<?php

namespace App\Models;

use DDragon\SanctumRefreshToken\HasRefreshTokens;
use Domain\UserPreferences\Models\UserPreferences;
use Domain\Users\DataTransferObjects\UpdateUserData;
use Domain\Users\DataTransferObjects\UserData;
use Domain\Users\Exceptions\UserNotFoundException;
use Domain\Users\Models\Role;
use Domain\Users\Notifications\SendQueuedEmailVerificationNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRefreshTokens;

    protected $fillable = [
        "name",
        "email",
        "password",
        "role_id",
        "phone_number",
    ];
    protected $hidden = ["password", "remember_token", "role_id"];
    protected $casts = ["email_verified_at" => "datetime"];

    /**
     * Check if there's a user with the provided ID
     *
     * @param integer $user_id
     *
     * @return void
     * @throws UserNotFoundException
     */
    public static function checkIfExists(int $user_id)
    {
        try {
            User::findOrFail($user_id);
        } catch (ModelNotFoundException $e) {
            throw new UserNotFoundException($user_id);
        }
    }

    public function preferences(): HasOne
    {
        return $this->hasOne(UserPreferences::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class, "role_id", "id");
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new SendQueuedEmailVerificationNotification());
    }

    /**
     * deletes any previous personal access token and refresh tokens for
     * the specified device id
     *
     * @param string $device_id
     *
     * @return void
     */
    public function deleteDeviceTokens(string $device_id): void
    {
        $this->tokens()
            ->where("name", $device_id)
            ->delete();
        $this->refreshTokens()
            ->where("name", $device_id)
            ->delete();
    }

    public function updateFromUserData(UpdateUserData $data): self
    {
        $this->email = $data->email ?? $this->email;
        $this->name = $data->name ?? $this->name;
        $this->phone_number = $data->phone_number ?? $this->phone_number;
        $this->role_id = $data->role_id ?? $this->role_id;
        return $this;
    }
}
