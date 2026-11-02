<?php

declare(strict_types=1);

namespace Core\Language\Nodes\Components;

use Core\Language\Nodes\AbstractNode;

class VariableNode extends AbstractNode
{
    public function __construct(?AbstractNode $parent, public readonly string $variableName)
    {
        parent::__construct($parent);
    }
}
