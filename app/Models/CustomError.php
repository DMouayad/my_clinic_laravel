<?php

namespace App\Models;

/**
 * Defines standard application error
 */
class CustomError
{
    public function __construct(
        public string $message = "",
        public ?int $code = null,
        public ?string $exception = null,
        public string|array|null $description = null
    ) {
    }

    public function __toString()
    {
        return $this->toJson();
    }

    public function toJson(): string
    {
        return json_encode([
            "message" => $this->message,
            "code" => strval($this->code),
            "exception" => $this->exception,
            "description" => $this->description,
        ]);
    }
}
