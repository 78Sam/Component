<?php

declare(strict_types=1);

$x = '---abc---def';
'     c';

class Block
{
    public function __construct(
        public readonly int $start,
        public readonly int $end,
        public readonly string $content,
    ) {
    }
}

class SpecificBlock extends Block
{}

class Token
{
    private int $left = 0;
    private int $right = 0;
    private string $context = '';

    private array $result = [];

    public function parse(string $context): array
    {
        $this->context = $context;
        $this->result = [];

        $char = $this->consume();
        while ($char !== null) {
            if (($char . implode('', $this->peak(2))) === '---') {
                $this->right--;
                $this->captureBlock(Block::class);
                $this->consume();
                $this->consume();
                $this->consume();
                $this->captureBlock(SpecificBlock::class);
            }
            $char = $this->consume();
        }

        return $this->result;
    }

    protected function captureBlock(string $model): void
    {
        $body = substr($this->context, $this->left, $this->right - $this->left);
        $this->result[] = new $model($this->left, $this->right, $body);
        $this->left = $this->right;
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

$tokenizer = new Token();
var_dump($tokenizer->parse($x));
