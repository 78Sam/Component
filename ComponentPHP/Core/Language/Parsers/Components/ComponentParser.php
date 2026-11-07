<?php

declare(strict_types=1);

namespace Core\Language\Parsers\Components;

use Core\Language\Exceptions\Components\NestedComponentException;
use Core\Language\Exceptions\ParseException;
use Core\Language\Exceptions\UnclosedTokenException;
use Core\Language\Nodes\AbstractNode;
use Core\Language\Nodes\BranchNodeInterface;
use Core\Language\Nodes\Components\ComponentNode;
use Core\Language\Nodes\Components\ConditionalNode;
use Core\Language\Nodes\Components\VariableNode;
use Core\Language\Nodes\ContainerNode;
use Core\Language\Nodes\NodeInterface;
use Core\Language\Nodes\RootNode;
use Core\Language\Nodes\StringValueNode;
use Core\Language\Parsers\AbstractParser;
use Core\Language\Tokens\AbstractToken;
use Core\Language\Tokens\Components\BodyToken;
use Core\Language\Tokens\Components\ComponentEndToken;
use Core\Language\Tokens\Components\ComponentStartToken;
use Core\Language\Tokens\Components\IfStatementElseToken;
use Core\Language\Tokens\Components\IfStatementEndToken;
use Core\Language\Tokens\Components\IfStatementStartToken;
use Core\Language\Tokens\Components\VariableToken;

class ComponentParser extends AbstractParser
{
    public function parse(array $tokens): RootNode
    {
        $generator = $this->tokenGenerator($tokens);

        $rootNode = new RootNode();
        while ($generator->valid()) {
            $rootNode->addChild($this->matchToken($generator, $rootNode, $generator->current()));
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
            IfStatementStartToken::class => $this->parseCondition($generator, $parent, $token),
            VariableToken::class => $this->parseVariable($generator, $parent, $token),
            default => throw new ParseException($tokenClass)
        };
    }

    /**
     * @param \Generator<int, AbstractToken, void, void> $generator
     */
    public function parseComponent(\Generator $generator, BranchNodeInterface $parent, ComponentStartToken $token): ComponentNode
    {
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

    /**
     * @param \Generator<int, AbstractToken, void, void> $generator
     */
    public function parseCondition(\Generator $generator, BranchNodeInterface $parent, IfStatementStartToken $token): ContainerNode
    {
        $hitElse = false;
        $condition = $token->getCondition();

        $container = new ContainerNode($parent);
        $ifStatementNode = new ConditionalNode($container, $condition);
        $container->addChild($ifStatementNode);

        while (($token = $this->getNext($generator)) !== null) {
            if ($token instanceof IfStatementEndToken) {
                return $container;
            }

            if ($token instanceof IfStatementElseToken) {
                if ($hitElse) {
                    throw new \Exception('Double else statement');
                }
                $hitElse = true;
                $ifStatementNode = new ConditionalNode($container, "!({$condition})");
                $container->addChild($ifStatementNode);

                continue;
            }

            $ifStatementNode->addChild($this->matchToken($generator, $ifStatementNode, $token));
        }

        $unclosedToken = $hitElse ? IfStatementElseToken::class : IfStatementStartToken::class;

        throw new UnclosedTokenException($unclosedToken);
    }

    public function parseVariable(\Generator $_generator, BranchNodeInterface $parent, VariableToken $token): VariableNode
    {
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
