<?php

declare(strict_types=1);

namespace Core\Components\Models\Components;

use Core\Components\Models\Sockets\SocketMap;

interface ComponentInterface
{
    public function __construct(string $name, SocketMap $socketMap);

    public function __toString(): string;

    public function render(): string;

    public function fill(string $name, string|ComponentInterface $value): self;

    /**
     * @param array<string, string|ComponentInterface> $values
     */
    public function fillArray(array $values): self;

    public function fillModel(object $model): self;
}
