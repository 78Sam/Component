<?php

declare(strict_types=1);

namespace Core\Language\Tokenizers;

use Core\Language\Tokens\AbstractToken;
use Core\Language\Tokens\Components\BodyToken;

abstract class AbstractTokenizer
{
    private string $context = '';
    private int $leftPosition = 0;
    private int $rightPosition = 0;
    private int $contextLength = 0;

    /** @var list<AbstractToken> */
    private array $result = [];

    abstract protected function handle(string $char);

    public function tokenize(string $context): array
    {
        $this->context = $context;
        $this->leftPosition = 0;
        $this->rightPosition = 0;
        $this->contextLength = strlen($this->context);

        while (!$this->isEof()) {
            $handler = $this->handle($this->peek(1)[0]);
            if ($handler) {
                $this->captureBlock(BodyToken::class);
                $handler();

                if ($this->isEof()) {
                    break;
                }
            }

            $this->consume();
        }

        return $this->result;
    }

    protected function isEof(): bool
    {
        return $this->rightPosition >= $this->contextLength;
    }

    /**
     * @return list<?string>
     */
    protected function peek(int $steps): array
    {
        $chars = [];
        for ($index = 0; $index < $steps; $index++) {
            $chars[] = $this->context[$this->rightPosition + $index] ?? null;
        }

        return $chars;
    }

    protected function consume(): string
    {
        if ($this->isEof()) {
            throw new \Exception('Attempted to read beyond EOF');
        }

        return $this->context[$this->rightPosition++];
    }

    /**
     * @param class-string<AbstractToken> $block
     */
    protected function captureBlock(string $block): self
    {
        $slice = substr($this->context, $this->leftPosition, $this->rightPosition - $this->leftPosition);
        $this->result[] = new $block($this->leftPosition, $this->rightPosition, $slice);
        $this->leftPosition = $this->rightPosition;

        return $this;
    }
}
