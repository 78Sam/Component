<?php

declare(strict_types=1);

namespace Core\Routing\Attributes;

use Core\Routing\Enums\RequestMethods;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class Route
{
    /** @var list<string> */
    public array $routes = [];

    /**
     * @param list<string> $routes
     * @param list<RequestMethods> $HTTPVerbs
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
