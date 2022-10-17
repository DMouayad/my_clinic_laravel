<?php

namespace Domain\UserPreferences\Models;

use App\Models\User;
use Domain\UserPreferences\DataTransferObjects\UserPreferencesData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents user app's theme and language preferences
 */
class UserPreferences extends Model
{
    use HasFactory;

    protected $fillable = ["theme", "locale", "user_id"];
    protected $hidden = ["id", "user_id", "created_at", "updated_at"];

    /**
     *
     * @param integer $user_id
     * @return \Domain\UserPreferences\Models\UserPreferences|null
     */
    public static function whereUserId(int $user_id): UserPreferences|null
    {
        return UserPreferences::where("user_id", $user_id)->first();
    }

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function updateFromData(UserPreferencesData $data): self
    {
        $this->theme = $data->theme ?? $this->theme;
        $this->locale = $data->locale ?? $this->locale;
        $this->user_id = $data->user_id ?? $this->user_id;
        return $this;
    }
}
