<?php

class SocketMap
{
    private array $sockets = [];

    private array $variableMap = [];

    private int $uid = 0;

    public function addVariable(string $variableName, string $value)
    {
        if (!array_key_exists($variableName, $this->variableMap)) {
            $this->variableMap[$variableName] = [];
        }

    }

    public function makeUnique(string $value): string
    {
        $x = $value . ($this->uid++);
        print_r($x . PHP_EOL);

        return $x;
    }
}

$y = new SocketMap();
$y->makeUnique('hi');
$y->makeUnique('hi');
$y->makeUnique('hi');
