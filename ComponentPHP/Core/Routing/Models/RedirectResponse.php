<?php

declare(strict_types=1);

namespace Core\Routing\Models;

class RedirectResponse extends Response
{
    public function __construct(string $location)
    {
        parent::__construct(
            content: '',
            responseCode: 302,
            headers: ['Location' => $location],
        );
    }
}
