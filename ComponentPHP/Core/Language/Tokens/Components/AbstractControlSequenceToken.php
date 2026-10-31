<?php

declare(strict_types=1);

namespace Core\Language\Tokens\Components;

use Core\Language\Tokens\AbstractToken;

readonly abstract class AbstractControlSequenceToken extends AbstractToken
{
    public const string CONTROL_START = '!@(';
    public const string CONTROL_END = ')';

    public function getBody(): ?string
    {
        return null;
    }

    abstract static function getIdentifier(): string;
}
