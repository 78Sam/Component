<?php

declare(strict_types=1);

namespace Core\Routing\Models\Responses;

class RedirectResponse extends Response
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(string $location, array $headers = [])
    {
        parent::__construct(content: '', responseCode: 302, headers: ['Location' => $location] + $headers);
    }
}
