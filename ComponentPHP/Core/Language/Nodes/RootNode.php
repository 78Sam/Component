<?php

declare(strict_types=1);

namespace Core\Language\Nodes;

class RootNode extends AbstractBranchNode
{
    public function __construct()
    {
        parent::__construct(null);
    }

    #[\Override]
    public function getParent(): null
    {
        return null;
    }
}
