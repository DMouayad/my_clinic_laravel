<?php

namespace App\Api\UserPreferences\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserPreferencesResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "theme" => $this->theme,
            "locale" => $this->locale,
        ];
    }
}
