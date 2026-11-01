<?php

declare(strict_types=1);

namespace Core\Language\Tokenizers\Components;

use Core\Language\Tokenizers\AbstractTokenizer;
use Core\Language\Tokens\Components\AbstractControlSequenceToken;
use Core\Language\Tokens\Components\BodyToken;
use Core\Language\Tokens\Components\ComponentEndToken;
use Core\Language\Tokens\Components\ComponentStartToken;
use Core\Language\Tokens\Components\IfStatementElseToken;
use Core\Language\Tokens\Components\IfStatementEndToken;
use Core\Language\Tokens\Components\IfStatementStartToken;
use Core\Language\Tokens\Components\VariableToken;

class ComponentTokenizer extends AbstractTokenizer
{
    #[\Override]
    protected function handle(string $char): void
    {
        if ($char !== '!') {
            return;
        }

        $currentThree = $char . implode('', $this->peak(2));
        if ($currentThree === AbstractControlSequenceToken::CONTROL_START) {
            $this->rightPosition--;
            $this->captureBlock(BodyToken::class);
            $this->parseControlSequence();
        }
    }

    private function parseControlSequence(): void
    {
        $controlSequenceString = '!'; // As we have already consumed the !
        do {
            $char = $this->consume();
            if ($char === null) {
                throw new \Exception("Couldn't find end for open control sequence");
            }
            $controlSequenceString .= $char;
        } while ($char !== AbstractControlSequenceToken::CONTROL_END);

        $controlSequenceString = str_replace(' ', '', $controlSequenceString);

        $key = match(1) {
            preg_match(IfStatementStartToken::getIdentifier(), $controlSequenceString) => IfStatementStartToken::class,
            preg_match(IfStatementElseToken::getIdentifier(), $controlSequenceString) => IfStatementElseToken::class,
            preg_match(IfStatementEndToken::getIdentifier(), $controlSequenceString) => IfStatementEndToken::class,
            preg_match(ComponentStartToken::getIdentifier(), $controlSequenceString) => ComponentStartToken::class,
            preg_match(ComponentEndToken::getIdentifier(), $controlSequenceString) => ComponentEndToken::class,
            preg_match(VariableToken::getIdentifier(), $controlSequenceString) => VariableToken::class,
            default => throw new \Exception("Unable to parse control sequence body '{$controlSequenceString}'"),
        };

        $this->captureBlock($key);
    }
}
