<?php

declare(strict_types=1);

namespace Core\Components\Models\Components;

use Core\Components\Models\Sockets\SocketMap;

abstract readonly class AbstractComponent implements ComponentInterface
{
    public function __construct(
        public string $name,
        public SocketMap $socketMap,
    ) {}

    public function __clone(): void
    {
        $this->socketMap = clone $this->socketMap;
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->render();
    }

    #[\Override]
    public function render(): string
    {
        return $this->socketMap->render();
    }

    #[\Override]
    public function fill(string $name, string|ComponentInterface $value): self
    {
        $this->socketMap->fill($name, $value);

        return $this;
    }

    #[\Override]
    public function fillArray(array $values): self
    {
        foreach ($values as $name => $value) {
            $this->fill($name, $value);
        }

        return $this;
    }

    public function fillModel(object $model)
    {
        // TODO: Use ReflectionService::getPropertyAccess() and SocketMap::getVariables()
    }
}
