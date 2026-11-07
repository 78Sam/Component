<?php

declare(strict_types=1);

namespace Core\Language\Nodes;

abstract class AbstractNode implements NodeInterface
{
    public function __construct(
        public readonly ?BranchNodeInterface $parent,
    ) {}
}
