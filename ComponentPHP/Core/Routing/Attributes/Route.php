<?php

declare(strict_types=1);

namespace Core\Routing\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD)]
final class Route
{
    /** @var list<string> */
    public array $routes = [];

    /**
     * @param list<string> $routes
     * @param list<string> $HTTPVerbs
     */
    public function __construct(
        array $routes,
        public readonly string $name,
        public readonly array $HTTPVerbs = [],
    ) {
        foreach ($routes as $route) {
            $this->routes[] = '/' . trim($route, '/');
        }
    }
}
