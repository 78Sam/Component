<?php

declare(strict_types=1);

namespace Core\Language\Tokenizers\Components;

use Core\Language\Tokenizers\AbstractTokenizer;
use Core\Language\Tokens\Components\AbstractControlSequenceToken;
use Core\Language\Tokens\Components\ComponentEndToken;
use Core\Language\Tokens\Components\ComponentStartToken;
use Core\Language\Tokens\Components\VariableToken;

class ComponentTokenizer extends AbstractTokenizer
{
    /** @var array<string, \Closure> */
    private array $controlKeys;
    private int $longestKey;

    public function __construct()
    {
        $this->controlKeys = [
            ComponentStartToken::IDENTIFIER => fn() => $this->captureControlSequence(ComponentStartToken::class),
            ComponentEndToken::IDENTIFIER => fn() => $this->captureControlSequence(ComponentEndToken::class),
            VariableToken::IDENTIFIER => fn() => $this->captureControlSequence(VariableToken::class),
        ];
        $this->longestKey = max(array_map(fn(string $key): int => strlen($key), array_keys($this->controlKeys)));
    }

    #[\Override]
    protected function handle(string $char)
    {
        if ($char !== '!') {
            return null;
        }

        $buffer = $this->quickPeek(3);
        if ($buffer !== AbstractControlSequenceToken::CONTROL_START) {
            return null;
        }

        for ($i = $this->longestKey; $i > 0; $i--) {
            $keyCheck = $this->quickPeek($i);
            if (!array_key_exists($keyCheck, $this->controlKeys)) {
                continue;
            }

            return $this->controlKeys[$keyCheck];
        }

        return null;
    }

    private function captureControlSequence(string $sequence): void
    {
        while ($this->consume() !== AbstractControlSequenceToken::CONTROL_END) {
            if ($this->isEof()) {
                throw new \Exception("Reached end of file while parsing {$sequence}");
            }
        }

        $this->captureBlock($sequence);
    }

    private function quickPeek(int $steps): string
    {
        return implode('', $this->peek($steps));
    }
}
