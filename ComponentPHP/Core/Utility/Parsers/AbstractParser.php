<?php

declare(strict_types=1);

namespace Core\Utility\Parsers;

use Core\Utility\Parsers\Exceptions\ParseException;

abstract class AbstractParser
{
    /**
     * @param array<string, mixed> $input
     */
    public function __construct(
        public array $input,
    ) {
    }

    /**
     * @template T
     * 
     * @param callable(mixed $value): (T|ParseException) $validator
     *
     * @throws ParseException
     * 
     * @return T
     */
    public function validate(string $key, callable $validator)
    {
        if (!array_key_exists($key, $this->input)) {
            throw new ParseException("Key '{$key}' doesn't exist");
        }

        $result = $validator($this->input[$key]);
        if ($result instanceof ParseException) {
            throw $result;
        }

        return $result;
    }

    /**
     * @template T
     * @template D
     * 
     * @param callable(mixed $value): (T|ParseException) $validator
     * @param D $default
     * 
     * @return T|D
     */
    public function validateWithDefault(string $key, callable $validator, mixed $default = null)
    {
        if (!array_key_exists($key, $this->input)) {
            return $default;
        }

        $result = $validator($this->input[$key]);
        if ($result instanceof ParseException) {
            return $default;
        }

        return $result;
    }
}
