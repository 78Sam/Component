<?php

declare(strict_types=1);

namespace Core\Routing\Models;

use Core\Routing\Attributes\Route;

readonly class SiteMapEntry2
{
    public bool $isDynamic;

    /**
     * @param \ReflectionAttribute<Route> $routeAttribute
     */
    public function __construct(
        public string $name,
        public string $route,
        public \ReflectionAttribute $routeAttribute,
    ) {
        $hasDynamicSegment = false;
        foreach (explode('/', $route) as $routeSegment) {
            if (preg_match(RouteSegment::PATTERN_PATTERN, $routeSegment) === 1) {
                $hasDynamicSegment = true;

                break;
            }
        }
        $this->isDynamic = $hasDynamicSegment;
    }
}
