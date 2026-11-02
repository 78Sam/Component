<?php

declare(strict_types=1);

namespace Core\Components\Models\Sockets;

abstract class AbstractSocket
{
    public function __construct(
        public readonly string $name,
        public string $value = '',
    ) {}
}
