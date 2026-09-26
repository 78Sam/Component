<?php

declare(strict_types=1);

namespace Core\Tests\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD)]
final readonly class Test
{
    /**
     * @param positive-int $priority Priority 1 goes first, larger priority value is run later
     */
    public function __construct(
        public string $description = '',
        public int $priority = 0,
        public bool $skipPreTest = false,
        public bool $skipPostTest = false,
    ) {}
}
