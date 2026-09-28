<?php

declare(strict_types=1);

namespace Core\Routing\Models;

class RouteSegment
{
    public const string SEGMENT_PATTERN = '/^(?<segment>[a-zA-Z0-9_-]+)$/';
    public const string PATTERN_PATTERN = '/^{(?<variable>[a-zA-Z]+[0-9]*)+(:(?<pattern>.*))?}$/';

    public function __construct(
        public readonly string $value,
        public readonly ?string $variable = null,
        public readonly bool $regex = false,
    ) {
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
