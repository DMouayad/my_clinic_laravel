<?php

namespace App\Traits;
trait ProvidesExceptionName
{
    public static function className(): array|string
    {
        return str_replace("App\\Exceptions\\", "", __CLASS__);
    }
}
