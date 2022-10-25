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

    public function broadcastOn()
    {
        return new PrivateChannel("notifications.user." . $this->user->id);
    }

    public function broadcastAs(): string
    {
        return "private-email-was-verified";
    }

    public function broadcastWith()
    {
        return [
            "id" => $this->user->id,
            "email_verified_at" => $this->user->email_verified_at,
        ];
    }
}
