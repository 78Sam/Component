<?php

declare(strict_types=1);

namespace App\Models;

readonly class User
{
    public function __construct(
        public string $username,
        public \DateTimeImmutable $accountCreatedAt,
    ) {
    }

    public function getAccountCreatedAt(): string
    {
        return $this->accountCreatedAt->format('Y-m-d H:i:s');
    }
}
