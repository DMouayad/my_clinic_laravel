<?php

namespace Database\Seeders\Utils;

class UserSeedingData
{
    const default_password = "clinic123";

    const emails = [
        "admin" => "admin@myclinic.com",
        "dentist" => "dentist@myclinic.com",
        "secretary" => "secretary@myclinic.com",
    ];
    
    public static function getRandomPhoneNum()
    {
        return str(random_int(min: 111111111, max: 999999999));
    }
}
