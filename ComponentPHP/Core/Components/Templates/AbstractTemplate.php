<?php

declare(strict_types=1);

namespace Core\Components\Templates;

use Core\Components\Exceptions\NonExistentComponentException;
use Core\Components\Models\Components\Component;
use Core\Components\Models\Components\ComponentInterface;
use Core\Components\Models\Sockets\SocketMap;
use Core\Components\Services\ComponentService;
use Core\Components\Templates\TemplateInterface;
use Core\Logging\Services\LoggingService;
use Core\Utility\Paths\Models\Path;
use Core\Utility\Paths\Models\PathRegistry;

abstract class AbstractTemplate implements TemplateInterface
{
    /** @var array<string, array<string, ComponentInterface>> */
    protected array $components = [];

    public function __construct(
        protected readonly ComponentService $componentService,
        protected readonly PathRegistry $pathRegistry,
        protected readonly LoggingService $loggingService,
    ) {
        foreach ($this->getFiles() as $key => $path) {
            $this->loggingService->log("Loading template file with key: '{$key}'", LoggingService::LEVEL_DEBUG);
            $this->loadFile($key, new Path($path));
        }
    }

    /**
     * @return array<string, string>
     */
    abstract protected function getFiles(): array;

    protected function loadFile(string $key, Path $path): self
    {
        if (array_key_exists($key, $this->components)) {
            return $this;
        }

        $this->pathRegistry->set($key, $path);
        $this->components[$key] = $this->componentService->loadFile($path);

        return $this;
    }

    protected function get(string $name, string $key): ComponentInterface
    {
        $component = $this->components[$key][$name] ?? null;
        if ($component === null) {
            throw new NonExistentComponentException($name, $key);
        }

        return clone $component;
    }

    /**
     * @param list<array<string, string>> $values
     *
     * @return list<ComponentInterface>
     */
    protected function stack(string $name, string $key, array $values): array
    {
        $components = [];
        foreach ($values as $value) {
            $components[] = $this->get($name, $key)->fillAll($value);
        }

        return $components;
    }

    /**
     * @param list<string|ComponentInterface> $items
     */
    protected function collect(array $items, string $separator = ''): ComponentInterface
    {
        $socketMap = new SocketMap();
        $totalItems = count($items);
        for ($i = 0; $i < ($totalItems - 1); $i++) {
            $item = $items[$i];
            if (!$item instanceof ComponentInterface && !is_string($item)) {
                $type = gettype($item);

                throw new \Exception("Cannot collect item that is not a string or component: '{$type}'");
            }
            $socketMap->add(SocketMap::PLACEHOLDER_KEY, $item);
            $socketMap->add(SocketMap::PLACEHOLDER_KEY, $separator);
        }
        $socketMap->add(SocketMap::PLACEHOLDER_KEY, $items[$totalItems]);

        return new Component('', $socketMap);
    }
}
