<?php

declare(strict_types=1);

namespace Core\Utility\Models;

/**
 * @template T
 */
abstract class AbstractRegistry
{
    /** @var array<string, T> */
    private array $items = [];

    /**
     * @template D
     *
     * @param D $default
     *
     * @return T|D
     */
    public function getWithDefault(string $key, mixed $default): mixed
    {
        if (!array_key_exists($key, $this->items)) {
            return $default;
        }

        return $this->items[$key];
    }

    /**
     * @return T
     */
    public function get(string $key): mixed
    {
        if (!array_key_exists($key, $this->items)) {
            throw new \Exception("Key '{$key}' not present in registry");
        }

        return $this->items[$key];
    }

    /**
     * @param T $value
     */
    public function set(string $key, mixed $value): static
    {
        $this->items[$key] = $value;

        return $this;
    }
}
