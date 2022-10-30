<?php

namespace Domain\Users\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class UserWasDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(readonly User $user)
    {
    }

    public function broadcastAs(): string
    {
        return "user-was-deleted";
    }

    public function broadcastOn()
    {
        return new PrivateChannel("users." . $this->user->id);
    }

    public function broadcastWith()
    {
        return [
            "user_id" => $this->user->id,
            "deleted_at" => now(),
        ];
    }
}
