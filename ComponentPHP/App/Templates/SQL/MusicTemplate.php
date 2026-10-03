<?php

declare(strict_types=1);

namespace App\Templates\SQL;

use Core\Components\Models\AbstractTemplate;
use Core\Utility\Services\PathService;

final class MusicTemplate extends AbstractTemplate
{
    #[\Override]
    protected function loadFiles(): void
    {
        $this->loadFile(PathService::fromProjectDirectory('App', 'Components', 'SQL', 'songs.sql'), true);
    }
}
