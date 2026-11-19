<?php

declare(strict_types=1);

namespace App\Templates\HTML;

use Core\Components\Templates\AbstractTemplate;

abstract class AbstractBaseTemplate extends AbstractTemplate
{
    public const string COMPONENT_HTML_CONTAINERS = 'html.containers';

    #[\Override]
    protected function getFiles(): array
    {
        return [
            self::COMPONENT_HTML_CONTAINERS => 'App/Components/HTML/containers.html',
        ];
    }
}
