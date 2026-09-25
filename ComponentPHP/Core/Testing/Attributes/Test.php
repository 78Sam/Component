<?php

declare(strict_types=1);

namespace Core\Testing\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD)]
final readonly class Test
{
    /**
     * @param int $priority Tests with the largest value go first
     */
    public function __construct(
        public string $description = '',
        public int $priority = 0,
    ) {}
}
