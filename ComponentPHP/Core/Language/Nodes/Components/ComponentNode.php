<?php

declare(strict_types=1);

namespace Core\Language\Nodes\Components;

use Core\Language\Nodes\AbstractBranchNode;
use Core\Language\Nodes\RootNode;

class ComponentNode extends AbstractBranchNode
{
    public function __construct(
        RootNode $parent,
        public readonly string $name,
    ) {
        parent::__construct($parent);
    }

    #[\Override]
    public function getParent(): RootNode
    {
        return $this->parent;
    }
}
