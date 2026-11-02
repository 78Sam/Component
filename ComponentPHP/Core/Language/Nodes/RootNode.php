<?php

declare(strict_types=1);

namespace Core\Language\Nodes;

class RootNode extends ContainerNode
{
    public function __construct(array $nodes = [])
    {
        parent::__construct(null, $nodes);
    }
}
