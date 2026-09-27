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

    protected static array $instances = [];

    private function __construct()
    {
        $this->componentService = new ComponentService();
        $this->init();
    }

    protected function init(): void {}

    public static function getInstance(): self
    {
        $class = static::class;
        if (!array_key_exists($class, static::$instances)) {
            static::$instances[$class] = new static();
        }

        return static::$instances[$class];
    }

    public function loadFile(string $path, bool $absolutePath = false): self
    {
        if ($absolutePath === false) {
            $path = relativeToAbsolutePath($path);
        }
        $path = normalisePath($path);

        if (array_key_exists($path, $this->componentsByFile)) {
            return $this;
        }

        $components = $this->componentService->loadFile($path, true);
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
}
