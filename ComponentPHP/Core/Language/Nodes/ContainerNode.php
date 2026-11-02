<?php

declare(strict_types=1);

namespace Core\Language\Nodes;

class ContainerNode extends AbstractNode
{
    /**
     * @param list<AbstractNode> $nodes
     */
    public function __construct(
        ?AbstractNode $parent,
        public array $nodes = [],
    ) {
        parent::__construct($parent);
    }
}
