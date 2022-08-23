<?php

namespace Database\Seeders\Utils;


trait ProvidesUserSeedingData
{
    public $default_password = 'clinic123';

    public  $users_emails = [
        'admin' => 'admin@myclinic.com',
        'dentist' => 'dentist@myclinic.com',
        'secretary' => 'secretary@myclinic.com',
    ];
}
