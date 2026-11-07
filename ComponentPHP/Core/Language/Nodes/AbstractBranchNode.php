<?php

declare(strict_types=1);

namespace Core\Language\Nodes;

abstract class AbstractBranchNode extends AbstractNode implements BranchNodeInterface
{
    /** @var list<NodeInterface> */
    protected array $children = [];

    public function __construct(?BranchNodeInterface $parent) {
        parent::__construct($parent);
    }

    public function addChild(NodeInterface $node): static
    {
        $this->children[] = $node;

        return $this;
    }

    #[\Override]
    public function getChildren(): array
    {
        return $this->children;
    }
}
