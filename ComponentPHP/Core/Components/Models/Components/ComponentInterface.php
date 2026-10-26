<?php

declare(strict_types=1);

namespace Core\Components\Models\Components;

interface ComponentInterface
{
    /**
     * @param array<string, string|ComponentInterface> $sockets
     * @param array<string, list<string>> $variableMap
     */
    public function __construct(string $name, array $sockets, array $variableMap);

    public function __toString(): string;

    public function render(): string;

    /**
     * @param array<string, mixed> $options
     */
    public function fill(string $name, mixed $value, array $options = []): self;

    /**
     * @param array<string, mixed> $values
     * @param array<string, mixed> $options
     */
    public function fillAll(array $values, array $options = []): self;
}
