<?php

declare(strict_types=1);

namespace Core\Components\Models\Components;

use Core\Components\Models\Sockets\SocketMap;

interface ComponentInterface
{
    public function __construct(string $name, SocketMap $socketMap);

    public function __toString(): string;

    public function render(): string;

    public function fill(string $name, string $value): self;

    /**
     * @param array<string, string> $values
     */
    public function fillAll(array $values): self;
}
