<?php

declare(strict_types=1);

namespace Core\Language\Tokens;

abstract readonly class AbstractToken
{
    public function __construct(
        public int $start,
        public int $end,
    ) {
    }
}
