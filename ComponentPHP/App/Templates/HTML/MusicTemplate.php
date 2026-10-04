<?php

declare(strict_types=1);

namespace App\Templates\HTML;

use Core\Components\Models\AbstractTemplate;
use Core\Utility\Services\PathService;

final class MusicTemplate extends AbstractTemplate
{
    #[\Override]
    protected function loadFiles(): void
    {
        $this->loadFile(PathService::fromProjectDirectory('App', 'Components', 'HTML', 'upload.html'), true);
        $this->loadFile(PathService::fromProjectDirectory('App', 'Components', 'HTML', 'songs.html'), true);
    }
}
