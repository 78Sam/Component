<?php

declare(strict_types=1);

namespace Core\Internals\Generated\Schematics;

use Core\Components\Models\Schematics\AbstractSchematic;

/**
 * @see ComponentPHP/Core/Internals/Components/dump.html
 */
final class DumpSchematic extends AbstractSchematic
{
    public const string _DUMP = 'dump';
    public const string _DATA_ITEM = 'data_item';

    #[\Override]
    protected static function getRawPath(): string
    {
        return 'Core/Internals/Components/dump.html';
    }
}
