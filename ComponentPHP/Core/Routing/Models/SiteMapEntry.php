<?php

declare(strict_types=1);

namespace Core\Routing\Models;

use Core\Routing\Attributes\Route;

final readonly class SiteMapEntry
{
    /**
     * @param list<RouteSegment> $segments
     */
    public function __construct(
        public Route $route,
        public \ReflectionClass $class,
        public \ReflectionMethod $method,
        public array $segments,
    ) {}

    public function isDynamic(): bool
    {
        foreach ($this->segments as $routeSegment) {
            if ($routeSegment->regex) {
                return true;
            }
        }

        return false;
    }
}
