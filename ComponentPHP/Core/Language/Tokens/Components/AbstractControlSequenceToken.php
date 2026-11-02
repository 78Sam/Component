<?php

declare(strict_types=1);

namespace Core\Language\Tokens\Components;

use Core\Language\Tokens\AbstractToken;

abstract readonly class AbstractControlSequenceToken extends AbstractToken
{
    public const string CONTROL_START = '!@(';
    public const string CONTROL_END = ')';

    abstract static function getIdentifier(): string;
}
