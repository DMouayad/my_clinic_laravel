<?php

namespace App\Exceptions;

use Exception;
use Support\Traits\ProvidesApiJsonResponse;

abstract class CustomException extends Exception
{
    use ProvidesApiJsonResponse;
}
