<?php

declare(strict_types=1);

namespace Core\DependencyInjection\Exceptions;

class InfiniteRecursionException extends \Exception
{
    public function __construct(
        string $classname,
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct("This service injection infinitely recurses, and has already been seen: '{$classname}'", $code, $previous);
    }
}
