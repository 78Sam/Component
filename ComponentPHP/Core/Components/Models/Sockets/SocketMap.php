<?php

declare(strict_types=1);

namespace Core\Components\Models\Sockets;

use Core\Components\Models\Components\ComponentInterface;

class SocketMap
{
    public const string PLACEHOLDER_KEY = '_block';

    /** @var array<string, string|ComponentInterface> */
    private array $sockets = [];

    /** @var array<string, list<string>> */
    private array $variableMap = [];

    private int $uid = 0;

    /**
     * @return list<string>
     */
    public function getVariables(): array
    {
        return array_keys($this->variableMap);
    }

    public function add(string $variableName, string|ComponentInterface $value): self
    {
        if (!array_key_exists($variableName, $this->variableMap)) {
            $this->variableMap[$variableName] = [];
        }

        $uniqueVariableName = $this->makeUnique($variableName);
        $this->variableMap[$variableName][] = $uniqueVariableName;
        $this->sockets[$uniqueVariableName] = $value;

        return $this;
    }

    public function fill(string $variableName, string|ComponentInterface $value): self
    {
        if (!array_key_exists($variableName, $this->variableMap)) {
            throw new \Exception("Socket '{$variableName}' does not exist");
        }

        foreach ($this->variableMap[$variableName] as $pseudonym) {
            $this->sockets[$pseudonym] = $value;
        }

        return $this;
    }

    public function render(): string
    {
        return implode('', $this->sockets);
    }

    private function makeUnique(string $value): string
    {
        return "{$value}_" . $this->uid++;
    }
}
