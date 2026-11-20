<?php

declare(strict_types=1);

namespace Core\Components\Templates;

use Core\Components\Exceptions\NonExistentComponentException;
use Core\Components\Models\Components\Component;
use Core\Components\Models\Components\ComponentInterface;
use Core\Components\Models\Components\ComponentRegistry;
use Core\Components\Models\Sockets\SocketMap;
use Core\Components\Services\ComponentService;
use Core\Components\Templates\TemplateInterface;
use Core\Logging\Services\LoggingService;
use Core\Utility\Paths\Models\Path;

abstract class AbstractTemplate implements TemplateInterface
{
    protected ComponentRegistry $componentRegistry;

    public function __construct(
        protected readonly ComponentService $componentService,
        protected readonly LoggingService $loggingService,
    ) {
        $this->componentRegistry = new ComponentRegistry();
        $this->setup();
    }

    abstract protected function setup(): void;

    protected function loadFile(Path $path): self
    {
        if ($this->componentRegistry->getWithDefault($path->getPath(), null) !== null) {
            return $this;
        }

        foreach ($this->componentService->loadFile($path) as $componentName => $component) {
            $this->componentRegistry->set("{$path->getPath()}.{$componentName}", $component);
        }

        return $this;
    }

    protected function get(string $name, Path $path): ComponentInterface
    {
        $component = $this->componentRegistry->getWithDefault("{$path->getPath()}.{$name}", null);
        if ($component === null) {
            throw new NonExistentComponentException($name, $path->getPath());
        }

        return clone $component;
    }

    /**
     * @param list<array<string, string>> $values
     *
     * @return list<ComponentInterface>
     */
    protected function stack(string $name, Path $path, array $values): array
    {
        $components = [];
        foreach ($values as $value) {
            $components[] = $this->get($name, $path)->fillAll($value);
        }

        return $components;
    }

    /**
     * @param list<string|ComponentInterface> $items
     */
    protected function collect(array $items, string $separator = ''): ComponentInterface
    {
        $validateType = static function(mixed $value): void {
            if (!$value instanceof ComponentInterface && !is_string($value)) {
                $type = gettype($value);

                throw new \Exception("Cannot collect item that is not a string or component: '{$type}'");
            }
        };

        $socketMap = new SocketMap();
        $totalItems = count($items);
        for ($i = 0; $i < ($totalItems - 1); $i++) {
            $item = $items[$i];
            $validateType($item);
            $socketMap->add(SocketMap::PLACEHOLDER_KEY, $item);
            $socketMap->add(SocketMap::PLACEHOLDER_KEY, $separator);
        }

        $lastItem = $items[$totalItems - 1];
        $validateType($lastItem);
        $socketMap->add(SocketMap::PLACEHOLDER_KEY, $lastItem);

        return new Component('', $socketMap);
    }
}
