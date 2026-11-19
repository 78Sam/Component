<?php

declare(strict_types=1);

namespace Core\Language\Parsers\Components;

use Core\Language\Exceptions\Components\NestedComponentException;
use Core\Language\Exceptions\ParseException;
use Core\Language\Exceptions\UnclosedTokenException;
use Core\Language\Nodes\AbstractNode;
use Core\Language\Nodes\BranchNodeInterface;
use Core\Language\Nodes\Components\ComponentNode;
use Core\Language\Nodes\Components\RootNode;
use Core\Language\Nodes\Components\VariableNode;
use Core\Language\Nodes\StringValueNode;
use Core\Language\Parsers\AbstractParser;
use Core\Language\Tokens\AbstractToken;
use Core\Language\Tokens\Components\BodyToken;
use Core\Language\Tokens\Components\ComponentEndToken;
use Core\Language\Tokens\Components\ComponentStartToken;
use Core\Language\Tokens\Components\VariableToken;

class ComponentParser extends AbstractParser
{
    // TODO: The generator is cool but a simple integer might be better

    public function parse(array $tokens): RootNode
    {
        $generator = $this->tokenGenerator($tokens);

        $rootNode = new RootNode();
        while ($generator->valid()) {
            $nextNode = $this->matchToken($generator, $rootNode, $generator->current());
            $rootNode->addChild($nextNode);
            $generator->next();
        }

        return $rootNode;
    }

    /**
     * @param \Generator<int, AbstractToken, void, void> $generator
     */
    public function matchToken(\Generator $generator, BranchNodeInterface $parent, AbstractToken $token): AbstractNode
    {
        $tokenClass = $token::class;

        return match ($tokenClass) {
            BodyToken::class => new StringValueNode($parent, $token->body),
            ComponentStartToken::class => $this->parseComponent($generator, $parent, $token),
            VariableToken::class => $this->parseVariable($generator, $parent, $token),
            default => throw new ParseException($tokenClass),
        };
    }

    /**
     * @param \Generator<int, AbstractToken, void, void> $generator
     */
    public function parseComponent(
        \Generator $generator,
        BranchNodeInterface $parent,
        ComponentStartToken $token,
    ): ComponentNode {
        if (!$parent instanceof RootNode) {
            throw new NestedComponentException();
        }

        $node = new ComponentNode($parent, $token->getName());
        while (($token = $this->getNext($generator)) !== null) {
            if ($token instanceof ComponentEndToken) {
                return $node;
            }
            $node->addChild($this->matchToken($generator, $node, $token));
        }

        throw new UnclosedTokenException(ComponentStartToken::class);
    }

    public function parseVariable(
        \Generator $_generator,
        BranchNodeInterface $parent,
        VariableToken $token,
    ): VariableNode {
        return new VariableNode($parent, $token->getName());
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
    private function getNext(\Generator $generator): ?AbstractToken
    {
        $generator->next();
        if (!$generator->valid()) {
            return null;
        }

        return $generator->current();
    }
}
