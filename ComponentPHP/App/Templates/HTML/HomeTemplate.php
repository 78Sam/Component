<?php

declare(strict_types=1);

namespace App\Templates;

use App\Templates\HTML\AbstractBaseTemplate;
use Core\Components\Models\Components\ComponentInterface;
use Core\Utility\Paths\Models\Path;

class HomeTemplate extends AbstractBaseTemplate
{
    public const array COMPONENT_KEY_PATH_MAPPINGS = [
        'app_template' => 'App/Components/app.html',
    ];

    public function getFiles()
    {
        return [
            'app_template' => 'App/Components/HTML/app.html',
        ];
    }

    #[\Override]
    protected function loadFiles(): void
    {
        parent::loadFiles();

        foreach ($this->getFiles() as $file) {
            
        }

        $this->pathRegistry->set(self::COMPONENT_KEY_APP, new Path(self::COMPONENT_KEY_PATH_MAPPINGS[self::COMPONENT_KEY_APP]));

        $this->loadFile($this->pathRegistry->get(self::COMPONENT_KEY_APP));
    }

    public function loadApp(string $title, string|ComponentInterface $body): ComponentInterface
    {
        return $this
            ->get(self::COMPONENT_KEY_APP, $this->pathRegistry->get(self::COMPONENT_KEY_APP))
            ->fill('title', $title)
            ->fill('body', $body)
        ;
    }
}
