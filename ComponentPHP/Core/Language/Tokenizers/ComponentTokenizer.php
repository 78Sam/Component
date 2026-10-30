<?php

declare(strict_types=1);

namespace Core\Language\Tokenizers;

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

        $controlSequenceBody = str_replace(' ', '', substr($controlSequenceString, 3, strlen($controlSequenceString) - 4));
        $key = match(true) {
            str_starts_with($controlSequenceBody, IfStatementStartToken::getIdentifier()) => IfStatementStartToken::class,
            str_starts_with($controlSequenceBody, IfStatementElseToken::getIdentifier()) => IfStatementElseToken::class,
            str_starts_with($controlSequenceBody, IfStatementEndToken::getIdentifier()) => IfStatementEndToken::class,
            str_starts_with($controlSequenceBody, ComponentStartToken::getIdentifier()) => ComponentStartToken::class,
            str_starts_with($controlSequenceBody, ComponentEndToken::getIdentifier()) => ComponentEndToken::class,
            str_starts_with($controlSequenceBody, VariableToken::getIdentifier()) => VariableToken::class,
            default => throw new \Exception("Unable to parse control sequence body '{$controlSequenceBody}'"),
        };

        $this->captureBlock($key);
    }
}
