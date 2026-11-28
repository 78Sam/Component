<?php

declare(strict_types=1);

namespace Core\Internals\Generated\Schematics;

use Core\Components\Models\Schematics\AbstractSchematic;

/**
 * @see ComponentPHP/Core/Internals/Components/schematic.php
 */
final class SchematicSchematic extends AbstractSchematic
{
    public const string _CLASS = 'class';
    public const string _COMPONENT_CONSTANT = 'component_constant';

    #[\Override]
    protected static function getRawPath(): string
    {
        return 'Core/Internals/Components/schematic.php';
    }
}
