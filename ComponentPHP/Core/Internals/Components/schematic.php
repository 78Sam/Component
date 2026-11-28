!@(component|class)
<?php

declare(strict_types=1);

namespace !@($namespace);

use Core\Components\Models\Schematics\AbstractSchematic;

/**
 * @see ComponentPHP/!@($path)
 */
final class !@($className)Schematic extends AbstractSchematic
{
    !@($componentConstants)

    protected static function getRawPath(): string
    {
        return '!@($path)';
    }
}
!@(end)

!@(component|component_constant)
public const string _!@($constantName) = '!@($name)';
!@(end)
