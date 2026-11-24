<?php

declare(strict_types=1);

namespace Core\Internals\Generated\Schematics;

use Core\Components\Models\Schematics\AbstractComponentSchematic;

class GetComponentFunctionComponentSchematic00000004 extends AbstractComponentSchematic
{
    public const string SOCKET_KEY = 'key';
    public const string SOCKET_NAME = 'name';

    #[\Override]
    public static function getName(): string
    {
        return 'get_component_function';
    }
}
