<?php

declare(strict_types=1);

namespace Core\Site\Generated\Schematics;

use Core\Components\Models\Components\AbstractSchematic;

final class DumpSchematic extends AbstractSchematic
{
    public const string COMPONENT_DUMP = 'dump';
    public const string COMPONENT_DATA_ITEM = 'data_item';

    #[\Override]
    protected static function getRawPath(): string
    {
        return 'Core/Site/Components/dump.html';
    }
}
