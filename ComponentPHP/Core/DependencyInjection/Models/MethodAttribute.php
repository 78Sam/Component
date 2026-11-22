<?php

declare(strict_types=1);

namespace Core\DependencyInjection\Models;

/**
 * @template T of object
 */
final readonly class MethodAttribute
{
    /**
     * @param \ReflectionAttribute<T> $attribute
     */
    public function __construct(
        public \ReflectionMethod $method,
        public \ReflectionAttribute $attribute,
    ) {
    }
}
