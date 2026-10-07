<?php

declare(strict_types=1);

namespace Core\Debug\Models;

use Core\Utility\Services\PathService;

readonly class Backtrace implements \Stringable
{
    public readonly string $file;
    public readonly string $line;

    public function __construct(
        ?string $file,
        ?int $line,
    ) {
        $this->file = $file !== null ? PathService::normalisePath($file) : 'Unknown file';
        $this->line = $line !== null ? "{$line}" : 'Unknown line';
    }

    public function __toString(): string
    {
        return "{$this->file}:{$this->line}";
    }
}
