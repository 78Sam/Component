<?php

declare(strict_types=1);

namespace Core\Language\Nodes\Components;

use Core\Language\Nodes\AbstractNode;

class ConditionalNode extends AbstractNode
{
    public function __construct(
        ?AbstractNode $parent,
        public readonly string $condition,
        public array $trueNodes = [],
        public array $falseNodes = [],
    ) {
        return parent::__construct($parent);
    }
}
