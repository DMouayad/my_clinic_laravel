<?php

namespace Support\Traits;
trait ExtractsExceptionName
{
    public static function className(): array|string
    {
        return str_replace("App\\Exceptions\\", "", __CLASS__);
    }
}
