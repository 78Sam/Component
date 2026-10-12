<?php

declare(strict_types=1);

namespace Core\Routing\Models;

use Core\Routing\Enums\RequestMethods;

readonly class Request
{
    public function __construct(
        public string $host,
        public string $scheme,
        public string $path,
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
