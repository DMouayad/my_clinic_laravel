<?php

namespace App\Exceptions;

class FailedToUpdateObjectException extends CustomException
{
    use ProvidesExceptionName;

    public function __construct(
        private string $objectClass,
        string $message = ""
    ) {
        parent::__construct($message);
    }

    public function render()
    {
        return $this->errorResponseFromException(
            $this,
            message: "Failed to delete object of type " . $this->objectClass
        );
    }
}
