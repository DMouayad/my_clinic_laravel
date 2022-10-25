<?php

namespace Domain\Users\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendQueuedEmailVerificationNotification extends VerifyEmail implements
    ShouldQueue,
    ShouldBeUnique
{
    use Queueable;

    public $tries = 5;
}
