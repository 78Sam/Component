<?php

declare(strict_types=1);

namespace Core\Database\Exceptions;

class NoModelConstructorException extends \Exception
{
    public function __construct(string $model, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct("The model '{$model}' must have a constructor", $code, $previous);
    }
}
