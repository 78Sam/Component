<?php

declare(strict_types=1);

namespace App\Templates\HTML;

use Core\Components\Templates\AbstractTemplate;
use Core\Utility\Paths\Models\Path;

abstract class AbstractBaseTemplate extends AbstractTemplate
{
    public const string COMPONENT_HTML_CONTAINERS = 'html.containers';

    #[\Override]
    protected function setup(): void
    {
        $this->loadFile(new Path('App/Components/HTML/containers.html'));
    }
}
