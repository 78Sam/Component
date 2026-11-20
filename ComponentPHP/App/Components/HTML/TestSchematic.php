<?php

declare(strict_types=1);

namespace App\Components\HTML;

use Core\Components\Models\Components\AbstractSchematic;

final class TestSchematic extends AbstractSchematic
{
    public const string COMPONENT_TEST = 'test';

    #[\Override]
    protected static function getRawPath(): string
    {
        return 'App/Components/HTML/test.html';
    }
}
