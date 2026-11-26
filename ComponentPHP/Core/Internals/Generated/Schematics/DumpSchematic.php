<?php

declare(strict_types=1);

namespace Core\Internals\Generated\Schematics;

use Core\Components\Models\Schematics\AbstractSchematic;

/**
 * @see ComponentPHP/Core/Internals/Components/dump_component.html
 */
final class DumpSchematic extends AbstractSchematic
{
    public const string COMPONENT_DUMP = 'dump';
    public const string COMPONENT_DATA_ITEM = 'data_item';

    #[\Override]
    protected static function getRawPath(): string
    {
        return 'Core/Internals/Components/dump_component.html';
    }
}
