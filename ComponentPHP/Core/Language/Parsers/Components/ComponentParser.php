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

    // public function validate(array $tokens): void
    // {
    //     $openedComponent = false;
    //     $controlStack = [];
    //     foreach ($tokens as $token) {

    //         if (!$token instanceof AbstractControlSequenceToken) {
    //             continue;
    //         }

    //         if ($token instanceof ComponentStartToken) {
    //             if ($openedComponent) {
    //                 throw new \Exception('Cannot open another component without closing the first');
    //             }
    //             $openedComponent = true;
    //             $controlStack[] = $token;

    //             continue;
    //         }

    //         // If it doesn't close any other control sequences add it as it must be an opener

    //         $closes = $token->closes();
    //         if ($closes === null) {
    //             if ($token instanceof ClosableTokenInterface) {
    //                 $controlStack[] = $token;
    //             }

    //             continue;
    //         }

    //         // If its meant to close something but the stack is empty there's an issue

    //         if ($controlStack === []) {
    //             throw new \Exception('Cannot close a non open control sequence');
    //         }

    //         $toClose = array_last($controlStack);
    //         assert($toClose !== null);
    //         $toClose = $toClose::class;

    //         // It must close one thing

    //         $closedAToken = false;
    //         foreach ($closes as $closedToken) {
    //             if ($closedToken !== $toClose) {
    //                 continue;
    //             }

    //             array_pop($controlStack);
    //             $closedAToken = true;
    //             if ($closedToken === ComponentStartToken::class) {
    //                 $openedComponent = false;
    //             }

    //             break;
    //         }

    //         if (!$closedAToken) {
    //             $class = $token::class;

    //             throw new \Exception("Tried to use a closing control sequence '{$class}' with nothing to close");
    //         }

    //         // It might still require closing its self, such as 'else'

    //         if ($token instanceof ClosableTokenInterface) {
    //             $controlStack[] = $token;
    //         }
    //     }

    //     if ($controlStack !== []) {
    //         throw new \Exception('Control stack not empty');
    //     }
    // }
}
