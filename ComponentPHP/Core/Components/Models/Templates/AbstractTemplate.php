<?php

declare(strict_types=1);

namespace Core\Components\Models;

use Core\Components\Exceptions\NonExistentComponentException;
use Core\Components\Models\Components\ComponentInterface;
use Core\Components\Models\Components\UnparsedComponent;
use Core\Components\Services\ComponentService;
use Core\Utility\Services\PathService;

abstract class AbstractTemplate implements TemplateInterface
{
    /** @var array<string, array<string, ComponentInterface>> */
    protected array $componentsByFile = [];

    // protected UnparsedComponent

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
        if (!file_exists($path)) {
            throw new \Exception("Unable to find file '{$path}'");
        }

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
        string $model,
        string $path,
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
            throw new NonExistentComponentException($componentName, $path);
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
        string $model,
        string $path,
        array $values,
        bool $absolutePath = false,
        array $options = [],
    ): array {
        $components = [];
        foreach ($values as $value) {
            $components[] = $this->get($componentName, $model, $path, $absolutePath)->fillAll($value, $options);
        }

        return $components;
    }

    /**
     * @template T of ComponentInterface
     *
     * @param list<string|ComponentInterface> $items
     * @param class-string<T> $model
     * 
     * @return T
     */
    public function collect(array $items, string $model, string $separator = ''): ComponentInterface
    {
        $sockets = [];
        for ($chunk = 0; $chunk < count($items); $chunk++) {
            $item = $items[$chunk];
            if (!$item instanceof ComponentInterface && !is_string($item)) {
                $type = gettype($item);

                throw new \Exception("Cannot collect item that is not a string or component: '{$type}'");
            }

            $sockets['_chunk_' . ($chunk * 2)] = $item;
            $sockets['_chunk_' . (($chunk * 2) + 1)] = $separator;
        }
        array_pop($sockets);

        return new $model('', $sockets, []);
    }

    protected function loadFiles(): void {}

    /**
     * @template T of ComponentInterface
     *
     * @param class-string<T> $model
     * 
     * @return T
     */
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

        // No need to check for duplicate names as the file must have been loaded and ComponentService::loadFile checks for dupes

        // if (array_key_exists($componentName, $this->componentsByFile[$path])) {
        //     throw new \Exception('Duplicate path components to be improved');
        // }

        $this->componentsByFile[$path][$componentName] = $component;

        return $component;
    }
}
