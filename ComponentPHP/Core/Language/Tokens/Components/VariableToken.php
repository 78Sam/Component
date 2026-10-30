<?php

declare(strict_types=1);

namespace Core\Language\Tokens\Components;

readonly class VariableToken extends AbstractControlSequenceToken
{
    #[\Override]
    public static function getIdentifier(): string
    {
        return '$';
    }

    #[\Override]
    public static function requiresClosing(): bool
    {
        return false;
    }

    #[\Override]
    public static function closes(): ?array
    {
        return null;
    }
}
