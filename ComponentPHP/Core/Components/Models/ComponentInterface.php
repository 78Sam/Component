<?php

declare(strict_types=1);

namespace Core\Components\Models;

interface ComponentInterface
{
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
