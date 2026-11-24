<?php

declare(strict_types=1);

namespace Core\Internals\Generated\Schematics;

use Core\Components\Models\Schematics\AbstractComponentSchematic;

class DataItemComponentSchematic00000002 extends AbstractComponentSchematic
{
    public const string SOCKET_DATA = 'data';

    #[\Override]
    public static function getName(): string
    {
        return 'data_item';
    }
}
