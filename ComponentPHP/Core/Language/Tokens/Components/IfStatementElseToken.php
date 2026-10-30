<?php

declare(strict_types=1);

namespace Core\Language\Tokens\Components;

readonly class IfStatementElseToken extends AbstractControlSequenceToken
{
    #[\Override]
    public static function getIdentifier(): string
    {
        return 'else';
    }

    #[\Override]
    public static function requiresClosing(): bool
    {
        return true;
    }

    #[\Override]
    public static function closes(): ?array
    {
        return [IfStatementStartToken::class];
    }
}
