<?php

declare(strict_types=1);

$x = '---abc---def';

class Token
{
    private int $left = 0;
    private int $right = 0;
    private string $context;

    public function parse(string $context): array
    {
        $this->context = $context;

        $char = $this->consume();
        while ($char !== null) {
            $this->consume();
        }

        
    }

    /**
     * @return list<?string>
     */
    protected function peak(int $steps): array
    {
        $chars = [];
        for ($index = 0; $index < $steps; $index++) {
            $chars[] = $this->context[$this->right + $index] ?? null;
        }

        return $chars;
    }

    protected function consume(): ?string
    {
        $char = $this->context[$this->right] ?? null;
        $this->right++;

        return $char;
    }
}
