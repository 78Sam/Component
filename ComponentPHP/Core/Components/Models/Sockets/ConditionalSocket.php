<?php

declare(strict_types=1);

namespace Core\Components\Models\Sockets;

class ConditionalSocket extends AbstractSocket
{
    public function __construct(
        string $name,
        string $value,
        public string $condition,
    ) {
        parent::__construct($name, $value);
    }
}
