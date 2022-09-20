<?php

namespace Database\Seeders\Utils;


use Illuminate\Support\Str;

trait ProvidesUserSeedingData
{
    public $default_password = 'clinic123';
    public $users_seeding_emails = [
        'admin' => 'admin@myclinic.com',
        'dentist' => 'dentist@myclinic.com',
        'secretary' => 'secretary@myclinic.com',
    ];

    public function getRandomPhoneNum()
    {
        return Str::random(9);
    }
}
