<?php

declare(strict_types=1);

namespace Core\Language\Tokens\Components;

use Core\Language\Tokens\ClosableTokenInterface;

readonly class IfStatementStartToken extends AbstractControlSequenceToken implements ClosableTokenInterface
{
    #[\Override]
    public static function getIdentifier(): string
    {
        return '/^!@\(if\|(?<condition>[ a-zA-Z0-9_$]+)\)$/';
    }
}
