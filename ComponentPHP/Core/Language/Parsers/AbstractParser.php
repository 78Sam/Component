<?php

declare(strict_types=1);

namespace Core\Language\Parsers;

use Core\Language\Nodes\AbstractNode;

abstract class AbstractParser implements ParserInterface
{
    abstract public function parse(array $tokens): AbstractNode;
}
