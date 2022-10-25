<?php

namespace Domain\Users\Exceptions;

use App\Exceptions\CustomException;
use Symfony\Component\HttpFoundation\Response;

class UserNotFoundException extends CustomException
{
    public function __construct(private readonly int $user_id)
    {
    }

    public function render()
    {
        return $this->errorResponseFromException(
            $this,
            "No user found with the id " . $this->user_id,
            Response::HTTP_NOT_FOUND
        );
    }
}
