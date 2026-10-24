<?php

declare(strict_types=1);

namespace Core\Components\Models;

class SQLComponent extends AbstractComponent
{
    #[\Override]
    public function fill(string $name, mixed $value, array $options = []): self
    {
        throw new \Exception('Not implemented');
    }
}
