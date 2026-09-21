<?php

declare(strict_types=1);

namespace Core\Components\Models;

class Component
{
    /**
     * @param array<string, string|Component> $sockets
     * @param array<string, list<string>> $variableMap
     */
    public function __construct(
        public readonly string $name,
        public array $sockets,
        public readonly array $variableMap,
    ) {
    }

    public function __toString(): string
    {
        return implode("", $this->sockets);
    }

    public function render(): string
    {
        return $this->__toString();
    }

    public function fill(string $name, string|Component $value, bool $raw = false): self
    {
        if (!array_key_exists($name, $this->variableMap)) {
            $options = implode(',', array_keys($this->variableMap));

            throw new \Exception("Cannot fill non-existent value '{$name}' options are '{$options}'");
        }

        $pseudonyms = $this->variableMap[$name];
        foreach ($pseudonyms as $pseudonym) {
            $this->sockets[$pseudonym] = $raw || $value instanceof Component ? $value : htmlspecialchars($value);
        }

        return $this;
    }
}
