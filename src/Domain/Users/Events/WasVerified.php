<?php

namespace Domain\Users\Events;

use Illuminate\Auth\Events\Verified;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class WasVerified extends Verified implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public function broadcastAs(): string
    {
        return "email-was-verified";
    }

    public function broadcastOn()
    {
        return new PrivateChannel("users." . $this->user->id);
    }

    public function broadcastWith()
    {
        return [
            "user_id" => $this->user->id,
            "email_verified_at" => $this->user->email_verified_at,
        ];
    }
}
