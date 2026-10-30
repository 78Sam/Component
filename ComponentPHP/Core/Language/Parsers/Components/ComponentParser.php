<?php

declare(strict_types=1);

namespace Core\Language\Parsers\Components;

use Core\Language\Parsers\AbstractParser;
use Core\Language\Tokens\Components\AbstractControlSequenceToken;
use Core\Language\Tokens\Components\ComponentStartToken;

class ComponentParser extends AbstractParser
{
    public function validate(array $tokens): void
    {
        $openedComponent = false;
        $controlStack = [];
        foreach ($tokens as $token) {

            if (!$token instanceof AbstractControlSequenceToken) {
                continue;
            }

            $tokenClass = $token::class;

            if ($token instanceof ComponentStartToken) {
                if ($openedComponent) {
                    throw new \Exception('Cannot open another component without closing the first');
                }
                $openedComponent = true;
                print_r("Opened '{$tokenClass}'\n");
                $controlStack[] = $token;

                continue;
            }

            // If it doesn't close any other control sequences add it as it must be an opener

            $closes = $token->closes();
            if ($closes === null) {
                if ($token->requiresClosing()) {
                    $controlStack[] = $token;
                    print_r("Opened '{$tokenClass}'\n");
                }

                continue;
            }

            // If its meant to close something but the stack is empty there's an issue

            if ($controlStack === []) {
                throw new \Exception('Cannot close a non open control sequence');
            }

            $toClose = array_last($controlStack);
            assert($toClose !== null);
            $toCloseClass = $toClose::class;

            print_r("Attempting to close '{$toCloseClass}'\n");

            // It must close one thing

            $closedAToken = false;
            foreach ($closes as $closedToken) {
                if ($closedToken === $toClose::class) {
                    array_pop($controlStack);
                    $closedAToken = true;
                    print_r("Closed '{$tokenClass}'\n");

                    break;
                }
            }

            if (!$closedAToken) {
                $class = $token::class;

                throw new \Exception("Tried to use a closing control sequence '{$class}' with nothing to close");
            }

            // It might still require closing its self, such as 'else'

            if ($token->requiresClosing()) {
                $controlStack[] = $token;
                print_r("Opened '{$tokenClass}'\n");
            }
        }
    }
}
