<?php

declare(strict_types=1);

namespace Core\Components\Models\Schematics;

use Core\Utility\Paths\Models\Path;
use Core\Utility\Paths\Models\PathRegistry;

abstract class AbstractSchematic
{
    protected static ?PathRegistry $pathRegistry = null;

    public static function getPath(): Path
    {
        if (static::$pathRegistry === null) {
            static::$pathRegistry = new PathRegistry();
        }

        $rawPath = static::getRawPath();
        $existingPath = static::$pathRegistry->getWithDefault($rawPath, null);
        if ($existingPath === null) {
            $existingPath = new Path($rawPath);
            static::$pathRegistry->set($rawPath, $existingPath);
        }

        return $existingPath;
    }

    abstract protected static function getRawPath(): string;
}
