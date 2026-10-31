<?php

declare(strict_types=1);

namespace Core\Language\Tokens\Components;

use Core\Language\Tokens\ClosableTokenInterface;

readonly class ComponentStartToken extends AbstractControlSequenceToken implements ClosableTokenInterface
{
    public const string PATTERN = '/!@\(\s*component|(?<component_name>\w+)\s*\)/';

    #[\Override]
    public function getBody(): string
    {
        $matches = [];
        preg_match(self::PATTERN, $this->body, $matches);

        return $matches['component_name'] ?? '';
    }

    #[\Override]
    public static function getIdentifier(): string
    {
        return 'component|';
    }
}
