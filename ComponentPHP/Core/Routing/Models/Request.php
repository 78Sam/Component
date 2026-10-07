<?php

declare(strict_types=1);

namespace Core\Routing\Models;

final readonly class Request
{
    public const string METHOD_GET = 'GET';
    public const string METHOD_POST = 'POST';
    public const string METHOD_PUT = 'PUT';
    public const string METHOD_PATCH = 'PATCH';

    public function __construct(
        public string $host,
        public string $scheme,
        public string $path,
        public int $port,
        public string $queryString,
        public string $method,
        public int $requestTime,
        public int $serverTime,
        public bool $isHTMX,
        public array $get = [],
        public array $post = [],
        public array $files = [],
        public array $cookies = [],
        public array $server = [],
    ) {}
}
