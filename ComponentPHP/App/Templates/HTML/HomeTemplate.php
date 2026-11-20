<?php

declare(strict_types=1);

namespace App\Templates\HTML;

use App\Components\HTML\AppSchematic;
use App\Components\HTML\TestSchematic;
use Core\Components\Models\Components\ComponentInterface;

class HomeTemplate extends AbstractBaseTemplate
{
    #[\Override]
    protected function setup(): void
    {
        parent::setup();

        $this->loadFile(AppSchematic::getPath());
        $this->loadFile(TestSchematic::getPath());
    }

    public function loadApp(string $title, string|ComponentInterface $body): ComponentInterface
    {
        return $this
            ->get(AppSchematic::COMPONENT_APP, AppSchematic::getPath())
            ->fill('title', $title)
            ->fill('body', $body)
        ;
    }

    public function loadTest(): ComponentInterface
    {
        return $this->get(TestSchematic::COMPONENT_TEST, TestSchematic::getPath());
    }
}
