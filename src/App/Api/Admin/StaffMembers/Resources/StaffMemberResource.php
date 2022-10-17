<?php

namespace App\Api\Admin\StaffMembers\Resources;

use App\Api\Users\Resources\RoleResource;
use App\Api\Users\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class StaffMemberResource extends JsonResource
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
            "created_at" => $this->created_at,
            "role" => new RoleResource($this->whenLoaded("role")),
            "user" => new UserResource($this->whenLoaded("user")),
        ];
    }
}
