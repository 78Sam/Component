<?php

declare(strict_types=1);

namespace Core\Language\Nodes;

interface NodeInterface
{
    public function getParent(): ?BranchNodeInterface;
}
