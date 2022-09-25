<?php

namespace App\Exceptions;

use App\Traits\ProvidesApiJsonResponse;
use Exception;

trait ProvidesExceptionName
{
    public static function className()
    {
        return str_replace('App\\Exceptions\\', '', __CLASS__);
    }
}

abstract class CustomException extends Exception
{
    use ProvidesApiJsonResponse, ProvidesExceptionName;
}
