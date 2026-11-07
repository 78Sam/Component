<?php

declare(strict_types=1);

namespace Core\Language\Nodes;

abstract class AbstractLeafNode extends AbstractNode implements LeafNodeInterface
{
    public function __construct(BranchNodeInterface $parent, public readonly string $value) {
        parent::__construct($parent);
    }

    #[\Override]
    public function getParent(): BranchNodeInterface
    {
        return $this->parent;
    }

    #[\Override]
    public function getValue(): string
    {
        return $this->value;
    }
}
