<?php

declare(strict_types=1);

namespace Core\Routing\Models;

use Core\Routing\Attributes\Route;

class SiteMapEntry
{
    public function __construct(
        public readonly Route $route,
        public readonly \ReflectionClass $class,
        public readonly \ReflectionMethod $method,
    ) {}
}
