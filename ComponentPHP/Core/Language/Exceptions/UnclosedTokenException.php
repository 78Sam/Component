<?php

declare(strict_types=1);

namespace Core\Language\Exceptions;

class UnclosedTokenException extends \Exception
{
    public function __construct(string $token)
    {
        parent::__construct("'{$token}' was never closed");
    }
}
