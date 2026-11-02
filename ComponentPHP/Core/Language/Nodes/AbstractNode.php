<?php

declare(strict_types=1);

namespace Core\Language\Nodes;

abstract class AbstractNode
{
    public function __construct(
        public readonly ?AbstractNode $parent,
    ) {
    }
}
