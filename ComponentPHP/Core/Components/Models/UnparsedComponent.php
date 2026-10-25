<?php

declare(strict_types=1);

namespace Core\Components\Models;

readonly class UnparsedComponent
{
    public function __construct(
        public string $name,
        public string $body,
    ) {}
}
