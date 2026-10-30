<?php

declare(strict_types=1);

namespace Core\Language\Tokens\Components;

readonly class IfStatementEndToken extends AbstractControlSequenceToken
{
    #[\Override]
    public static function getIdentifier(): string
    {
        return 'endif';
    }

    #[\Override]
    public static function requiresClosing(): bool
    {
        return false;
    }

    #[\Override]
    public static function closes(): ?array
    {
        return [IfStatementStartToken::class, IfStatementElseToken::class];
    }
}
