<?php

declare(strict_types=1);

namespace Core\Internals\Generated\Schematics;

use Core\Components\Models\Schematics\AbstractComponentSchematic;

class DumpComponentSchematic00000001 extends AbstractComponentSchematic
{
    public const string SOCKET_DEBUG_FRAME = 'debug_frame';
    public const string SOCKET_DATA = 'data';

    #[\Override]
    public static function getName(): string
    {
        return 'dump';
    }
}
