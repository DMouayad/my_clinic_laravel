<?php

namespace App\Exceptions;

use Exception;
use App\Traits\ProvidesApiJsonResponse;

class CustomException extends Exception
{
    use ProvidesApiJsonResponse;
}
