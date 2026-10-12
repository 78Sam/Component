<?php

declare(strict_types=1);

namespace Core\Routing\Models\Responses;

use Core\Routing\Enums\ResponseCode;

readonly class RedirectResponse extends Response
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(string $location, array $headers = [])
    {
        parent::__construct(content: '', responseCode: ResponseCode::Found, headers: ['Location' => $location]
        + $headers);
    }
}
