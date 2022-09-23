<?php

namespace Database\Seeders\Utils;

trait ProvidesUserSeedingData
{
    public $default_password = "clinic123";
    public $users_seeding_emails = [
        "admin" => "admin@myclinic.com",
        "dentist" => "dentist@myclinic.com",
        "secretary" => "secretary@myclinic.com",
    ];

    public function getRandomPhoneNum()
    {
        return str(random_int(min: 111111111, max: 999999999));
    }
}
