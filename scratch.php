<?php

declare(strict_types=1);

class ValidationException extends \Exception
{
    public function __construct(string $message = "")
    {
        return parent::__construct($message);
    }
}

abstract class RequestValidatorInterface
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
     * @param callable(mixed $value): (T|ValidationException) $validator
     *
     * @throws ValidationException
     * 
     * @return T
     */
    public function validate(string $key, callable $validator)
    {
        if (!array_key_exists($key, $this->input)) {
            throw new ValidationException('Cannot find key');
        }

        $result = $validator($this->input[$key]);
        if ($result instanceof ValidationException) {
            throw $result;
        }

        return $result;
    }

    /**
     * @template T
     * @template D
     * 
     * @param callable(mixed $value): (T|ValidationException) $validator
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
        if ($result instanceof ValidationException) {
            return $default;
        }

        return $result;
    }
}

class RequestValidator extends RequestValidatorInterface
{
    public function validateString(mixed $value): string|ValidationException
    {
        if (is_string($value)) {
            return $value;
        }

        return new ValidationException('Failed to validate string');
    }
}

$input = [
    'required_1' => 'stringValue',
    'required_2' => 'intstring',
    'optional_1' => 12,
];

$validator = new RequestValidator($input);

$x = $validator->validateWithDefault('required_1', $validator->validateString(...), 'sam');
echo $x;