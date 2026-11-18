<?php

declare(strict_types=1);

namespace Core\Utility\Paths\Models;

use Core\Utility\Paths\Services\PathService;

class Path
{
    private ?string $path = null;

    /**
     * @param bool $absolutePath For use with paths that point outside the project directory
     */
    public function __construct(
        private readonly string $rawPath,
        private readonly bool $absolutePath = false,
    ) {
    }

    public function __toString(): string
    {
        return $this->getPath();
    }

    public function getPath(): string
    {
        if ($this->path === null) {
            $this->path = $this->absolutePath ? PathService::normalisePath($this->rawPath) : PathService::fromProjectDirectory($this->rawPath);
        }

        return $this->path;
    }
}
