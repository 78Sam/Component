<?php

declare(strict_types=1);

namespace Core\Language\Tokens\Components;

use Core\Language\Tokens\ClosableTokenInterface;
use Core\Language\Tokens\ClosingTokenInterface;

readonly class IfStatementElseToken extends AbstractControlSequenceToken implements
    ClosableTokenInterface,
    ClosingTokenInterface
{
    #[\Override]
    public static function getIdentifier(): string
    {
        return '/^!@\(else\)$/';
    }

    #[\Override]
    public static function closes(): array
    {
        return [IfStatementStartToken::class];
    }
}
