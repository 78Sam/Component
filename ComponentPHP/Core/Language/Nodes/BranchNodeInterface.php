<?php

declare(strict_types=1);

namespace Core\Language\Nodes;

interface BranchNodeInterface extends NodeInterface
{
    /**
     * @return list<NodeInterface>
     */
    public function getChildren(): array;
}
