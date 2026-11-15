<?php

declare(strict_types=1);

namespace Core\Components\Models\Components;

use Core\Components\Models\Sockets\SocketMap;

readonly class Component
{
    public function __construct(
        public string $name,
        public SocketMap $socketMap,
    ) {
    }

    public function __clone(): void
    {
        $this->socketMap = clone $this->socketMap;
    }

    public function __toString(): string
    {
        return $this->render();
    }

    public function render(): string
    {
        return $this->socketMap->render();
    }

    public function fill(string $variableName, string $value): self
    {
        $this->socketMap->fill($variableName, $value);

        return $this;
    }
}
