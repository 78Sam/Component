<?php

declare(strict_types=1);

namespace Core\Language\Tokenizers;

use Core\Language\Tokens\AbstractToken;

abstract class AbstractTokenizer implements TokenizerInterface
{
    private ?string $context = null;

    private int $leftPosition = 0;
    private int $rightPosition = 0;

    /** @var list<AbstractToken> */
    private array $result = [];

    abstract protected function handle(string $char): void;

    public function tokenize(string $context): array
    {
        $this->context = $context;

        $char = $this->consume();
        while ($char !== null) {
            $this->handle($char);
            $char = $this->consume();
        }

        return $this->result;
    }

    /**
     * @return list<?string>
     */
    protected function peak(int $steps): array
    {
        $chars = [];
        for ($index = 0; $index < $steps; $index++) {
            $chars[] = $this->context[$this->rightPosition + $index] ?? null;
        }

        return $chars;
    }

    protected function consume(): ?string
    {
        $char = $this->context[$this->rightPosition] ?? null;
        $this->rightPosition++;

        return $char;
    }

    /**
     * @param class-string<AbstractToken> $block
     */
    protected function captureBlock(string $block): self
    {
        $this->result[] = new $block($this->leftPosition, $this->rightPosition);
        $this->leftPosition = $this->rightPosition;

        return $this;
    }
}
