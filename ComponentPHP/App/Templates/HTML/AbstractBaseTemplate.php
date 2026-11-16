<?php

declare(strict_types=1);

namespace App\Templates\HTML;

use Core\Components\Services\ComponentService;
use Core\Components\Templates\AbstractTemplate;
use Core\Utility\Paths\Models\PathRegistry;

abstract class AbstractBaseTemplate extends AbstractTemplate
{
    public const string COMPONENT_KEY_CONTAINERS = 'containers';

    public function __construct(
        ComponentService $componentService,
        protected readonly PathRegistry $pathRegistry,
    ) {
        parent::__construct($componentService);
    }

    #[\Override]
    protected function loadFiles(): void
    {
        parent::loadFiles();
    }
}
