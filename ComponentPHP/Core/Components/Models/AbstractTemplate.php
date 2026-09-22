<?php

declare(strict_types=1);

namespace Core\Components\Models;

use Core\Components\Services\ComponentService;

abstract class AbstractTemplate
{
    public readonly ComponentService $componentService;

    /** @var array<string, Component> */
    public array $componentsByName = [];

    /** @var array<string, array<string, Component>> */
    public array $componentsByFile = [];

    public function __construct()
    {
        $this->componentService = new ComponentService();
    }

    public function loadFile(string $path, bool $absolutePath = false): self
    {
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
        $component = $path !== null ? ($this->componentsByFile[$path][$name] ?? null) : ($this->componentsByName[$name] ?? null);

        return $component === null ? null : clone $component;
    }
}
