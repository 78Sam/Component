<?php

declare(strict_types=1);

namespace App\Models;

readonly class User
{
    public function __construct(
        public string $username,
    ) {
    }
}
