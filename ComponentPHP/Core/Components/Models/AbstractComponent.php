<?php

declare(strict_types=1);

namespace Core\Components\Models;

abstract class AbstractComponent implements ComponentInterface
{
    /**
     * @param array<string, string|ComponentInterface> $sockets
     * @param array<string, list<string>> $variableMap
     */
    public function __construct(
        public readonly string $name,
        public array $sockets,
        public readonly array $variableMap,
    ) {
    }

    public function __clone(): void
    {
        foreach ($this->sockets as $key => $socket) {
            if ($socket instanceof ComponentInterface) {
                $this->sockets[$key] = clone $socket;
            }
        }
    }

    #[\Override]
    public function __toString(): string
    {
        return implode('', $this->sockets);
    }

    #[\Override]
    public function render(): string
    {
        return $this->__toString();
    }

    /**
     * @param array<string, mixed> $values
     */
    public function fillAll(array $values): self
    {
        foreach ($values as $name => $value) {
            $this->fill($name, $value);
        }

        return $this;
    }
}
