<?php

declare(strict_types=1);

namespace Core\Language\Nodes\Components;

use Core\Language\Nodes\AbstractBranchNode;
use Core\Language\Nodes\BranchNodeInterface;

class ConditionalNode extends AbstractBranchNode
{
    public function __construct(
        BranchNodeInterface $parent,
        public readonly string $condition,
    ) {
        parent::__construct($parent);
    }

    #[\Override]
    public function getParent(): BranchNodeInterface
    {
        return $this->parent;
    }
}
