<?php

namespace App\Exceptions;

use App\Traits\ProvidesApiJsonResponse;
use Exception;

abstract class CustomException extends Exception
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
