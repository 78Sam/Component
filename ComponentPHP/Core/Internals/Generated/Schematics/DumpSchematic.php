<?php

declare(strict_types=1);

namespace Core\Internals\Generated\Schematics;

use Core\Components\Models\Schematics\AbstractSchematic;

/**
 * @see ComponentPHP/Core/Internals/Components/dump_component.html
 */
final class DumpSchematic extends AbstractSchematic
{
    private static ?DumpComponentSchematic00000001 $cDump = null;
    public static function cDump(): DumpComponentSchematic00000001
    {
        if (self::$cDump === null) {
            self::$cDump = new DumpComponentSchematic00000001();
        }
        
        return self::$cDump;
    }

    private static ?DataItemComponentSchematic00000002 $cDataItem = null;
    public static function cDataItem(): DataItemComponentSchematic00000002
    {
        if (self::$cDataItem === null) {
            self::$cDataItem = new DataItemComponentSchematic00000002();
        }
        
        return self::$cDataItem;
    }

    #[\Override]
    protected static function getRawPath(): string
    {
        return 'Core/Internals/Components/dump_component.html';
    }
}
