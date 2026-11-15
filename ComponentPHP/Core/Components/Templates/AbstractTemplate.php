<?php

declare(strict_types=1);

namespace Core\Components\Templates;

use Core\Components\Exceptions\NonExistentComponentException;
use Core\Components\Models\Components\Component;
use Core\Components\Models\Components\ComponentInterface;
use Core\Components\Models\Sockets\SocketMap;
use Core\Components\Services\ComponentService;
use Core\Components\Templates\TemplateInterface;
use Core\Utility\Paths\Models\Path;

abstract class AbstractTemplate implements TemplateInterface
{
    /** @var array<string, array<string, ComponentInterface>> */
    protected array $components = [];

    public function __construct(protected readonly ComponentService $componentService) {
        $this->loadFiles();
    }

    protected function loadFiles(): void {}

    #[\Override]
    public function loadFile(Path $path): self
    {
        if (array_key_exists($path->getPath(), $this->components)) {
            return $this;
        }

        $this->components[$path->getPath()] = $this->componentService->loadFile($path);

        return $this;
    }

    #[\Override]
    public function get(string $name, Path $path): ComponentInterface
    {
        $component = $this->components[$path->getPath()][$name] ?? null;
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
    public function stack(string $name, Path $path, array $values): array
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
    public function collect(array $items, string $separator = ''): ComponentInterface
    {
        $socketMap = new SocketMap();
        $totalItems = count($items);
        for ($i = 0; $i < $totalItems - 1; $i++) {
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
