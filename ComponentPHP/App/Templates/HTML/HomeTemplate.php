<?php

declare(strict_types=1);

namespace App\Templates\HTML;

use Core\Components\Models\Components\ComponentInterface;

class HomeTemplate extends AbstractBaseTemplate
{
    private const string COMPONENT_HTML_APP = 'html.app';

    #[\Override]
    public function getFiles(): array
    {
        return array_merge(
            parent::getFiles(),
            [
                self::COMPONENT_HTML_APP => 'App/Components/HTML/app.html',
            ],
        );
    }

    public function loadApp(string $title, string|ComponentInterface $body): ComponentInterface
    {
        return $this
            ->get('app', self::COMPONENT_HTML_APP)
            ->fill('title', $title)
            ->fill('body', $body)
        ;
    }
}
