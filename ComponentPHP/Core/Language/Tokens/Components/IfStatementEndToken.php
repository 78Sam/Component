<?php

declare(strict_types=1);

namespace Core\Language\Tokens\Components;

use Core\Language\Tokens\ClosingTokenInterface;

readonly class IfStatementEndToken extends AbstractControlSequenceToken implements ClosingTokenInterface
{
    #[\Override]
    public static function getIdentifier(): string
    {
        return '/!@\(endif\)/';
    }

    #[\Override]
    public static function closes(): array
    {
        return [IfStatementStartToken::class, IfStatementElseToken::class];
    }
}
