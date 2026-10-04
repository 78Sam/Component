<?php

declare(strict_types=1);

namespace App\Templates\HTML;

use Core\Components\Models\AbstractTemplate;
use Core\Components\Models\Component;
use Core\Utility\Services\PathService;

class RootTemplate extends AbstractTemplate
{
    #[\Override]
    protected function loadFiles(): void
    {
        $this->loadFile(PathService::fromProjectDirectory('App', 'Components', 'HTML', 'app.html'), true);
        $this->loadFile(PathService::fromProjectDirectory('App', 'Components', 'HTML', 'navigation.html'), true);
    }

    public function getApp(string|Component $body): Component
    {
        return $this
            ->get('app')
            ->fill('body', $this->collect([$this->get('nav_bar'), $body]))
        ;
    }
}
