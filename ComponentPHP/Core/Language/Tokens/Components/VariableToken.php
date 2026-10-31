<?php

declare(strict_types=1);

namespace Core\Language\Tokens\Components;

readonly class VariableToken extends AbstractControlSequenceToken
{
    public const string PATTERN = '/!@\(\s*\$(?<variable_name>\w+)\s*\)/';

    #[\Override]
    public function getBody(): string
    {
        $matches = [];
        preg_match(self::PATTERN, $this->body, $matches);

        return $matches['variable_name'] ?? null;
    }

    #[\Override]
    public static function getIdentifier(): string
    {
        return '$';
    }
}
