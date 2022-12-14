<?php

namespace App\Api\Users\Resources;

use App\Api\UserPreferences\Resources\UserPreferencesResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "email" => $this->email,
            "name" => $this->name,
            "phone_number" => $this->phone_number,
            "role" => $this->whenLoaded("role"),
            "preferences" => new UserPreferencesResource(
                $this->whenLoaded("preferences")
            ),
            "email_verified_at" => $this->email_verified_at,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
