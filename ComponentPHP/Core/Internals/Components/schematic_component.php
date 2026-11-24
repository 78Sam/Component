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
    !@($getFunctions)

    protected static function getRawPath(): string
    {
        return '!@($path)';
    }
}
!@(end)

!@(component|get_component_function)
private static ?!@($key) $c!@($name) = null;
public static function c!@($name): !@($key)
{
    if (self::$c!@($name) === null) {
        self::$c!@($name) = new !@($key)();
    }
    
    return self::$c!@($name);
}
!@(end)
