<?php

declare(strict_types=1);

namespace Core\Routing\Models\Responses;

use Core\Components\Models\Component;
use Core\Routing\Enums\ResponseCode;

readonly class Response
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        public string|Component $content,
        public ResponseCode $responseCode = ResponseCode::Ok,
        public string $contentType = 'text/html',
        public string $charset = 'utf-8',
        public array $headers = [],
    ) {}
}
