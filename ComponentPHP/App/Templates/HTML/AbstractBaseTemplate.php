<?php

declare(strict_types=1);

namespace App\Templates\HTML;

use App\Components\HTML\ContainersSchematic;
use Core\Components\Templates\AbstractTemplate;

abstract class AbstractBaseTemplate extends AbstractTemplate
{
    #[\Override]
    protected function setup(): void
    {
        $this->loadFile(ContainersSchematic::getPath());
    }
}
