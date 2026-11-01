<?php

declare(strict_types=1);

namespace Core\Language\Tokens\Components;

use Core\Language\Tokens\ClosableTokenInterface;

readonly class ComponentStartToken extends AbstractControlSequenceToken implements ClosableTokenInterface
{
    #[\Override]
    public static function getIdentifier(): string
    {
        return '/^!@\(component\|(?<component_name>\w+)\)$/';
    }
}
