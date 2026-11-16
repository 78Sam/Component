<?php

declare(strict_types=1);

namespace Core\Utility\Validators\Exceptions;

class ParseException extends \Exception
{
    public function __construct(string $message = '')
    {
        return parent::__construct($message);
    }
}
