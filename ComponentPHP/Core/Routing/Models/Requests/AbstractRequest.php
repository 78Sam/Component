<?php

declare(strict_types=1);

use Core\Routing\Enums\RequestMethods;

abstract readonly class AbstractRequest
{
    public function __construct(
        public string $host,
        public string $scheme,
        public string $route,
        public int $port,
        public string $queryString,
        public RequestMethods $method,
        public int $requestTime,
        public int $serverTime,
        public array $get = [],
        public array $post = [],
        public array $files = [],
        public array $cookies = [],
        public array $server = [],
    ) {}
}
