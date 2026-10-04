<?php

declare(strict_types=1);

namespace Core\Components\Models;

use Core\Components\Services\ComponentService;

abstract class AbstractTemplate
{
    /** @var array<string, Component> */
    public array $componentsByName = [];

    /** @var array<string, array<string, Component>> */
    public array $componentsByFile = [];

    public function __construct(
        public readonly ComponentService $componentService,
    ) {
        $this->loadFiles();
    }

    public function loadFile(string $path, bool $absolutePath = false): self
    {
        if (array_key_exists($path, $this->componentsByFile)) {
            return $this;
        }

        $components = $this->componentService->loadFile($path, $absolutePath);
        foreach ($components as $name => $component) {
            $this->componentsByName[$name] = $component;
            $this->componentsByFile[$path][$name] = $component;
        }

        return $this;
    }

    public function get(string $name, ?string $path = null): ?Component
    {
        /** @var ?Component $component */
        $component = $path !== null
            ? $this->componentsByFile[$path][$name] ?? null
            : $this->componentsByName[$name] ?? null;

        return $component === null ? null : clone $component;
    }

    /**
     * @param list<array<string, int|string|Component>> $values
     * 
     * @return list<Component>
     */
    public function stack(string $component, array $values, bool $raw = false): array
    {
        $components = [];
        foreach ($values as $value) {
            $components[] = $this->get($component)->fillAll($value, $raw);
        }

        return $components;
    }

    /**
     * @param list<string|Component> $items
     */
    public function collect(array $items, string $separator = ''): Component
    {
        $sockets = [];
        for ($chunk = 0; $chunk < \count($items); $chunk++) {
            $sockets["_chunk_{$chunk}"] = $items[$chunk];
            $sockets['_chunk_' . ($chunk + 1)] = $separator;
        }
        array_pop($sockets);

        return new Component('', $sockets, []);
    }

    /**
     * @param list<array<string, int|string|Component>> $values
     */
    public function quickCollect(string $component, array $values, string $separator = '', bool $raw = false): Component
    {
        return $this->collect($this->stack($component, $values, $raw), $separator);
    }

    protected function loadFiles(): void {}
}
