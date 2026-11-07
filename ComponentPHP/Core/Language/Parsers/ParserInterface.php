<?php

declare(strict_types=1);

namespace Core\Language\Parsers;

use Core\Language\Nodes\AbstractNode;
use Core\Language\Tokens\AbstractToken;

interface ParserInterface
{
    /**
     * @param list<AbstractToken> $tokens
     */
    public function parse(array $tokens): AbstractNode;
}
