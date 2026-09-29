<?php

declare(strict_types=1);

namespace App\Models;

readonly class User
{
    public const int ROLE_USER = 0;
    public const int ROLE_ADMIN = 1;

    public function __construct(
        public string $username,
        public \DateTimeImmutable $joined,
        public int $role,
    ) {
    }
}
