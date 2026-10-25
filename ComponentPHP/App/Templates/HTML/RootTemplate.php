<?php

declare(strict_types=1);

namespace App\Templates\HTML;

use Core\Components\Models\AbstractTemplate;
use Core\Components\Models\HTMLComponent;

class RootTemplate extends AbstractTemplate
{
    public const string APP_FILE = 'App/Components/HTML/app.html';
    public const string NAVIGATION_FILE = 'App/Components/HTML/navigation.html';

    #[\Override]
    protected function loadFiles(): void
    {
        $this->loadFile(self::APP_FILE);
        $this->loadFile(self::NAVIGATION_FILE);
    }

    public function getApp(string|HTMLComponent $body): HTMLComponent
    {
        return $this
            ->get('app', HTMLComponent::class, self::APP_FILE)
            ->fill('body', $this->collect([$this->get('nav_bar', HTMLComponent::class, self::NAVIGATION_FILE), $body], HTMLComponent::class))
        ;
    }
}
