<?php

declare(strict_types=1);

namespace Core\Language\Exceptions;

class ParseException extends \Exception
{
    public function __construct(string $token)
    {
        parent::__construct("Cannot parse '{$token}' here");
    }
}
