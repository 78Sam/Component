<?php

declare(strict_types=1);

namespace Core\Internals\Generated\Schematics;

use Core\Components\Models\Schematics\AbstractComponentSchematic;

class ClassComponentSchematic00000003 extends AbstractComponentSchematic
{
    public const string SOCKET_GET_FUNCTIONS = 'getFunctions';
    public const string SOCKET_NAME = 'name';
    public const string SOCKET_NAMESPACE = 'namespace';
    public const string SOCKET_PATH = 'path';

    #[\Override]
    public static function getName(): string
    {
        return 'class';
    }
}
