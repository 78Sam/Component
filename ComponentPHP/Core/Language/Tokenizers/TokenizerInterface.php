<?php

declare(strict_types=1);

namespace Core\Language\Tokenizers;

use Core\Language\Tokens\AbstractToken;

interface TokenizerInterface
{
    /**
     * @return list<AbstractToken>
     */
    public function tokenize(string $context): array;
}
