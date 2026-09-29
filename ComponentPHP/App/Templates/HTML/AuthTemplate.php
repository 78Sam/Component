<?php

declare(strict_types=1);

namespace App\Templates\HTML;

use Core\Components\Models\AbstractTemplate;
use Core\Utility\Services\PathService;

class AuthTemplate extends AbstractTemplate
{
    #[\Override]
    protected function loadFiles(): void
    {
        $this->loadFile(PathService::fromProjectDirectory('App', 'Components', 'HTML', 'auth.html'), true);
    }
}
