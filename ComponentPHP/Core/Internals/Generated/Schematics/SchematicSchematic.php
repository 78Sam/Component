<?php

declare(strict_types=1);

namespace Core\Internals\Generated\Schematics;

use Core\Components\Models\Schematics\AbstractSchematic;

/**
 * @see ComponentPHP/Core/Internals/Components/schematic_component.php
 */
final class SchematicSchematic extends AbstractSchematic
{
    public static function cClass(): ClassComponentSchematic00000003
    {
        return new ClassComponentSchematic00000003();
    }

    public static function cGetComponentFunction(): GetComponentFunctionComponentSchematic00000004
    {
        return new GetComponentFunctionComponentSchematic00000004();
    }

    #[\Override]
    protected static function getRawPath(): string
    {
        return 'Core/Internals/Components/schematic_component.php';
    }
}
