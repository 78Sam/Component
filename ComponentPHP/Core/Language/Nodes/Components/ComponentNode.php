<?php

declare(strict_types=1);

namespace Core\Language\Nodes\Components;

use Core\Language\Nodes\AbstractNode;
use Core\Language\Nodes\ContainerNode;

class ComponentNode extends ContainerNode
{
    public function __construct(
        ?AbstractNode $parent,
        public readonly string $name,
        array $nodes = [],
    ) {
        parent::__construct($parent, $nodes);
    }
}
