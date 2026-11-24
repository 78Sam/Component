<?php

declare(strict_types=1);

namespace Core\Components\Models\Schematics;

abstract class AbstractComponentSchematic
{
    abstract static function getName(): string;
}
