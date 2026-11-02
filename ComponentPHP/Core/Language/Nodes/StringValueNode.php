<?php

declare(strict_types=1);

namespace Core\Language\Nodes;

class StringValueNode extends AbstractNode
{
    public function __construct(
        ?AbstractNode $parent,
        public readonly string $value,
    ) {
        parent::__construct($parent);
    }
}
