<?php

declare(strict_types=1);

namespace Core\Components\Models;

use Core\Components\Services\ComponentService;
use Core\Utility\Services\PathService;

abstract class AbstractTemplate2 implements TemplateInterface
{
    /** @var array<string, array<string, ComponentInterface>> */
    protected array $componentsByFile = [];

    /** @var array<string, array<string, UnparsedComponent>> */
    protected array $unparsedComponentsByFile = [];

    public function __construct(
        protected readonly ComponentService $componentService,
    ) {
        $this->loadFiles();
    }

    public function loadFile(string $path, bool $absolutePath = false): self
    {
        $path = $absolutePath ? PathService::normalisePath($path) : PathService::fromProjectDirectory($path);

        if (array_key_exists($path, $this->unparsedComponentsByFile)) {
            return $this;
        }

        $this->unparsedComponentsByFile[$path] = $this->componentService->loadFile($path, true);

        return $this;
    }

    /**
     * @template T of ComponentInterface
     *
     * @param class-string<T> $model
     *
     * @return T
     */
    public function get(
        string $componentName,
        string $path,
        string $model,
        bool $absolutePath = false,
    ): ComponentInterface {
        $path = $absolutePath ? PathService::normalisePath($path) : PathService::fromProjectDirectory($path);

        // Do we just have it
        $component = $this->componentsByFile[$path][$componentName] ?? null;
        if ($component !== null) {
            return clone $component;
        }

        // Attempt to load it, no-op if we already have
        $this->loadFile($path, true);

        $unparsedComponent = $this->unparsedComponentsByFile[$path][$componentName] ?? null;
        if ($unparsedComponent === null) {
            throw new \Exception("Component doesn't exist"); // TODO: Improve
        }

        return clone $this->parseNewComponent($unparsedComponent, $model, $path);
    }

    /**
     * @template T of ComponentInterface
     *
     * @param class-string<T> $model
     * @param array<string, mixed> $values
     * @param array<string, mixed> $options
     *
     * @return list<T>
     */
    public function stack(
        string $componentName,
        string $path,
        string $model,
        array $values,
        bool $absolutePath = false,
        array $options = [],
    ): array {
        $components = [];
        foreach ($values as $value) {
            $components[] = $this->get($componentName, $path, $model, $absolutePath)->fillAll($value, $options);
        }

        return $components;
    }

    protected function loadFiles(): void {}

    private function parseNewComponent(
        UnparsedComponent $unparsedComponent,
        string $model,
        string $path,
    ): ComponentInterface {
        $component = $this->componentService->parseComponent($unparsedComponent, $model);
        $componentName = $unparsedComponent->name;
        if (!array_key_exists($path, $this->componentsByFile)) {
            $this->componentsByFile[$path] = [];
        }

        if (array_key_exists($componentName, $this->componentsByFile[$path])) {
            throw new \Exception('Duplicate path components to be improved'); // TODO: Improve
        }

        $this->componentsByFile[$path][$componentName] = $component;

        return $component;
    }
}
