<?php

declare(strict_types=1);

namespace Core\Components\Models\Sockets;

class SocketMap
{
    /** @var array<string, string> */
    private array $sockets = [];

    /** @var array<string, list<string>> */
    private array $variableMap = [];

    private int $uid = 0;

    public function add(string $variableName, string $value): self
    {
        if (!array_key_exists($variableName, $this->variableMap)) {
            $this->variableMap[$variableName] = [];
        }

        $uniqueVariableName = $this->makeUnique($variableName);
        $this->variableMap[$variableName][] = $uniqueVariableName;
        $this->sockets[$uniqueVariableName] = $value;

        return $this;
    }
    
    public function fill(string $variableName, string $value): self
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
        return "{$value}_" . ($this->uid++);
    }
}
