<?php

namespace App\Exceptions;

use Exception;
use App\Traits\ProvidesApiJsonResponse;

class CustomException extends Exception
{
    use ProvidesApiJsonResponse, ProvidesClassName;
}
trait ProvidesClassName
{
    public static function className()
    {
        return str_replace('App\\Exceptions\\', '', __CLASS__);
    }
}
