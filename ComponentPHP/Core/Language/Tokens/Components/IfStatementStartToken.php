<?php

declare(strict_types=1);

namespace Core\Language\Tokens\Components;

readonly class IfStatementStartToken extends AbstractControlSequenceToken
{
    #[\Override]
    public static function getIdentifier(): string
    {
        return 'if|';
    }

    #[\Override]
    public static function requiresClosing(): bool
    {
        return true;
    }

    #[\Override]
    public static function closes(): ?array
    {
        return null;
    }
}
