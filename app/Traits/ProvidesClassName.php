<?php

namespace App\Traits;
trait ProvidesClassName
{
    public static function className(): array|string
    {
        return str_replace("App\\Exceptions\\", "", __CLASS__);
    }
}
