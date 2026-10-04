<?php

declare(strict_types=1);

namespace App\Models;

final readonly class Song
{
    public function __construct(
        public int $id,
        public string $title,
        public string $artist,
        public User $addedBy,
        public \DateTimeImmutable $addedAt,
        public int $duration,
        public string $lookup_key,
    ) {
    }
}
