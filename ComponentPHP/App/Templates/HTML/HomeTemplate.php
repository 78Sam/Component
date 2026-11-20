<?php

declare(strict_types=1);

namespace App\Templates\HTML;

use App\Components\HTML\Test;
use Core\Components\Models\Components\ComponentInterface;
use Core\Utility\Paths\Models\Path;

class HomeTemplate extends AbstractBaseTemplate
{
    private const string COMPONENT_HTML_APP = 'html.app';

    #[\Override]
    protected function setup(): void
    {
        parent::setup();

        $this->loadFile(new Path('App/Components/HTML/app.html'));
    }

    public function loadApp(string $title, string|ComponentInterface $body): ComponentInterface
    {
        return $this
            ->get('app', new Path('App/Components/HTML/app.html'))
            ->fill('title', $title)
            ->fill('body', $body)
        ;
    }

    public function loadTest(): ComponentInterface
    {
        $this->loadFile(Test::getPath());

        return $this->get('test', 'test');
    }
}
