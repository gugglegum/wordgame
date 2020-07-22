<?php

declare(strict_types = 1);

namespace App\Exceptions\Http;

class Http403ForbiddenException extends HttpException
{
    public function __construct(string $message = "", \Throwable $previous = null)
    {
        parent::__construct($message, 403, $previous);
    }

}
