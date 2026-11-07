<?php

declare(strict_types=1);

namespace Core\Language\Nodes;

class ContainerNode extends AbstractBranchNode
{
    public function __construct(BranchNodeInterface $parent)
    {
        parent::__construct($parent);
    }

    #[\Override]
    public function getParent(): BranchNodeInterface
    {
        return $this->parent;
    }
}
