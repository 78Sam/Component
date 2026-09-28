<?php

declare(strict_types=1);

namespace Core\Routing\Models\Responses;

use Core\Components\Models\Component;

class Response
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        public readonly string|Component $content,
        public readonly int $responseCode = 200, // TODO(Sam): This could be an enum for the response codes
        public readonly string $contentType = 'text/html',
        public readonly string $charset = 'utf-8',
        public readonly array $headers = [],
    ) {}
}
