<?php

declare(strict_types=1);

namespace Core\Database\Exceptions;

class NoConnectionException extends \Exception
{
    public function __construct(
        string $message = 'You must connect to a database before attempting to run a query',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
