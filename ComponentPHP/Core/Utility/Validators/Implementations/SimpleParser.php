<?php

declare(strict_types=1);

namespace Core\Utility\Validators\Implementations;

use Core\Utility\Validators\AbstractParser;
use Core\Utility\Validators\Exceptions\ParseException;

class SimpleParser extends AbstractParser
{
    public function validateString(mixed $value): string|ParseException
    {
        if (is_string($value)) {
            return $value;
        }

        return new ParseException('Not a string');
    }

    public function validateInt(mixed $value): int|ParseException
    {
        if (is_int($value)) {
            return $value;
        }

        if (ctype_digit($value)) {
            return (int) $value;
        }

        $type = gettype($value);

        return new ParseException("Not an int, got '{$value}' of type '{$type}'");
    }
}
