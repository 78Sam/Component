<?php

declare(strict_types=1);

namespace App\Components\HTML;

use Core\Components\Models\Components\AbstractSchematic;

final class ContainersSchematic extends AbstractSchematic
{
    public const string COMPONENT_SIMPLE_CONTAINER = 'simple_container';

    #[\Override]
    protected static function getRawPath(): string
    {
        return 'App/Components/HTML/containers.html';
    }
}
