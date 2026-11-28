!@(component|class)
<?php

declare(strict_types=1);

namespace !@($namespace);

use Core\Components\Models\Schematics\AbstractSchematic;

/**
 * @see ComponentPHP/!@($path)
 */
final class !@($name)Schematic extends AbstractSchematic
{
    !@($componentConstants)

    protected static function getRawPath(): string
    {
        return '!@($path)';
    }
}
!@(end)

!@(component|component_constant)
public const string !@($constantName) = '!@($name)';
!@(end)
