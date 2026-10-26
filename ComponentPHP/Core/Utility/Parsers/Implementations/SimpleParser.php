<?php

declare(strict_types=1);

namespace Core\Utility\Parsers\Implementations;

use Core\Utility\Parsers\AbstractParser;
use Core\Utility\Parsers\Exceptions\ParseException;

class SimpleParser extends AbstractParser
{
    public function validateString(mixed $value): string|ParseException
    {
        if (is_string($value)) {
            return $value;
        }

        return new ParseException('Not a string');
    }
}
