<?php

declare(strict_types=1);

namespace Core\Language\Parsers\Components;

use Core\Language\Exceptions\ClosingUnopenedTokenException;
use Core\Language\Exceptions\Components\NestedComponentException;
use Core\Language\Exceptions\UnmatchedClosingTokenException;
use Core\Language\Parsers\AbstractParser;
use Core\Language\Tokens\AbstractToken;
use Core\Language\Tokens\ClosableTokenInterface;
use Core\Language\Tokens\ClosingTokenInterface;
use Core\Language\Tokens\Components\AbstractControlSequenceToken;
use Core\Language\Tokens\Components\ComponentStartToken;

class ComponentParser extends AbstractParser
{
    /**
     * @param list<AbstractToken> $tokens
     * 
     * @throws ClosingUnopenedTokenException If you try and close a sequence but there are none to close
     * @throws UnmatchedClosingTokenException If you try and close a sequence with the wrong token
     * @throws NestedComponentException If you try and open another component without closing the first
     */
    public function validate(array $tokens): void
    {
        $componentOpen = false;
        $sequenceStack = [];
        foreach ($tokens as $token) {

            if (!$token instanceof AbstractControlSequenceToken) {
                continue;
            }

            // A control token can open and close others

            if ($token instanceof ClosingTokenInterface) {
                if ($sequenceStack === []) {
                    throw new ClosingUnopenedTokenException($token);
                }

                /** @var AbstractToken $tokenToClose */
                $tokenToClose = array_pop($sequenceStack);
                if (!in_array($tokenToClose::class, $token->closes(), true)) {
                    throw new UnmatchedClosingTokenException($token);
                }

                if ($tokenToClose instanceof ComponentStartToken) {
                    $componentOpen = false;
                }
            }

            if ($token instanceof ClosableTokenInterface) {
                if ($token instanceof ComponentStartToken) {
                    if ($componentOpen) {
                        throw new NestedComponentException();
                    }
                    $componentOpen = true;
                }
                $sequenceStack[] = $token;
            }

        }
    }
}
