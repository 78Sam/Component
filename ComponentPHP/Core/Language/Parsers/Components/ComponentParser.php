<?php

declare(strict_types=1);

namespace Core\Language\Parsers\Components;

use Core\Language\Exceptions\ClosingUnopenedTokenException;
use Core\Language\Exceptions\Components\NestedComponentException;
use Core\Language\Exceptions\UnclosedTokenException;
use Core\Language\Exceptions\UnmatchedClosingTokenException;
use Core\Language\Nodes\AbstractNode;
use Core\Language\Nodes\Components\ComponentNode;
use Core\Language\Nodes\Components\ConditionalNode;
use Core\Language\Nodes\Components\VariableNode;
use Core\Language\Nodes\ContainerNode;
use Core\Language\Nodes\RootNode;
use Core\Language\Nodes\StringValueNode;
use Core\Language\Parsers\AbstractParser;
use Core\Language\Tokens\AbstractToken;
use Core\Language\Tokens\ClosableTokenInterface;
use Core\Language\Tokens\ClosingTokenInterface;
use Core\Language\Tokens\Components\AbstractControlSequenceToken;
use Core\Language\Tokens\Components\BodyToken;
use Core\Language\Tokens\Components\ComponentEndToken;
use Core\Language\Tokens\Components\ComponentStartToken;
use Core\Language\Tokens\Components\IfStatementElseToken;
use Core\Language\Tokens\Components\IfStatementEndToken;
use Core\Language\Tokens\Components\IfStatementStartToken;
use Core\Language\Tokens\Components\VariableToken;

class ComponentParser extends AbstractParser
{
    /**
     * @param list<AbstractToken> $tokens
     *
     * @throws ClosingUnopenedTokenException If you try and close a sequence but there are none to close
     * @throws UnmatchedClosingTokenException If you try and close a sequence with the wrong token
     * @throws NestedComponentException If you try and open another component without closing the first
     * @throws UnclosedTokenException If there was a closable token that wasn't closed
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

        $unclosedToken = array_last($sequenceStack);
        if ($unclosedToken !== null) {
            throw new UnclosedTokenException($unclosedToken);
        }
    }

    /**
     * @param list<AbstractToken> $tokens
     */
    public function parse(array $tokens): AbstractNode
    {
        $generator = $this->tokenGenerator($tokens);

        $currentNode = new RootNode();
        while ($generator->valid()) {
            $currentNode->nodes[] = $this->matchToken($generator, $currentNode, $generator->current());
            $generator->next();
        }

        return $currentNode;
    }

    /**
     * @param \Generator<int, AbstractToken, void, void> $generator
     */
    public function matchToken(\Generator $generator, AbstractNode $parent, AbstractToken $token): AbstractNode
    {
        $tokenClass = $token::class;

        return match ($tokenClass) {
            BodyToken::class => new StringValueNode($parent, $token->body),
            ComponentStartToken::class => $this->parseComponent($generator, $parent),
            IfStatementStartToken::class => $this->parseCondition($generator, $parent),
            VariableToken::class => new VariableNode($parent, 'TODO VAR NAME'),
            default => throw new \Exception("Cannot do '{$tokenClass}'"),
        };
    }

    /**
     * @param \Generator<int, AbstractToken, void, void> $generator
     */
    public function parseComponent(\Generator $generator, ContainerNode $parent): ComponentNode
    {
        if (!$parent instanceof RootNode) {
            throw new \Exception('Component parent must be root node');
        }

        $node = new ComponentNode($parent, 'TODO NAME');
        while (!($token = $this->getNext($generator)) instanceof ComponentEndToken) {
            $node->nodes[] = $this->matchToken($generator, $node, $token);
        }

        return $node;
    }

    /**
     * @param \Generator<int, AbstractToken, void, void> $generator
     */
    public function parseCondition(\Generator $generator, ContainerNode $parent): ConditionalNode
    {
        $hitElse = false;
        $node = new ConditionalNode($parent, 'TODO CONDITION');
        while (!($token = $this->getNext($generator)) instanceof IfStatementEndToken) {
            if ($token instanceof IfStatementElseToken) {
                if ($hitElse) {
                    throw new \Exception('Double else statement');
                }
                $hitElse = true;

                continue;
            }

            if ($hitElse) {
                $node->falseNodes[] = $this->matchToken($generator, $node, $token);

                continue;
            }

            $node->trueNodes[] = $this->matchToken($generator, $node, $token);
        }

        return $node;
    }

    /**
     * @param list<AbstractToken> $tokens
     *
     * @return \Generator<int, AbstractToken, void, void>
     */
    private function tokenGenerator(array $tokens): \Generator
    {
        foreach ($tokens as $token) {
            yield $token;
        }
    }

    /**
     * @param \Generator<int, AbstractToken, void, void> $generator
     */
    private function getNext(\Generator $generator): AbstractToken
    {
        $generator->next();

        return $generator->current();
    }
}
