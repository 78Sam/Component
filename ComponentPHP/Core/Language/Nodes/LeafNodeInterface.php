<?php

declare(strict_types=1);

namespace Core\Language\Nodes;

interface LeafNodeInterface extends NodeInterface
{
    public function getValue(): string;

    #[\Override]
    public function getParent(): BranchNodeInterface;
}
