<?php

declare(strict_types=1);

namespace Core\Language\Tokens\Components;

use Core\Language\Tokens\ClosingTokenInterface;

readonly class ComponentEndToken extends AbstractControlSequenceToken implements ClosingTokenInterface
{
    #[\Override]
    public static function getIdentifier(): string
    {
        return '/^!@\(end\)$/';
    }

    #[\Override]
    public static function closes(): array
    {
        return [ComponentStartToken::class];
    }
}
