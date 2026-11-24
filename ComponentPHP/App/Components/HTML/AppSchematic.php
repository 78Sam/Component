<?php

declare(strict_types=1);

namespace App\Components\HTML;

use Core\Components\Models\Schematics\AbstractSchematic;

final class AppSchematic extends AbstractSchematic
{
    public const string COMPONENT_APP = 'app';

    #[\Override]
    protected static function getRawPath(): string
    {
        return 'App/Components/HTML/app.html';
    }
}
