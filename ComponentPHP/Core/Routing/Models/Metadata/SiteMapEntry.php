<?php

declare(strict_types=1);

namespace Core\Routing\Models\Metadata;

use Core\Routing\Attributes\Route;
use Core\Utility\Services\PathService;

final readonly class SiteMapEntry
{
    /** @var list<RouteSegment> */
    public array $routeSegments;

    /**
     * @param \ReflectionAttribute<Route> $routeAttribute
     */
    public function __construct(
        public string $name,
        public string $route,
        public \ReflectionAttribute $routeAttribute,
        public \ReflectionClass $controller,
        public \ReflectionMethod $method,
    ) {
        $routeSegments = [];
        foreach (PathService::splitRoute($route) as $segment) {
            $routeSegments[] = new RouteSegment($segment);
        }
        $this->routeSegments = $routeSegments;
    }

    public function asPattern(): string
    {
        return '#^/' . implode('/', $this->routeSegments) . '$#'; // Using # to avoid having to escape /
    }

    public function isDynamic(): bool
    {
        foreach ($this->routeSegments as $routeSegment) {
            if ($routeSegment->isDynamic) {
                return true;
            }
        }

        return false;
    }
}
