<?php

declare(strict_types=1);

namespace App\Templates;

use App\Templates\HTML\AbstractBaseTemplate;
use Core\Components\Models\Components\ComponentInterface;
use Core\Utility\Paths\Models\Path;

class HomeTemplate extends AbstractBaseTemplate
{
    private const string COMPONENT_KEY_APP = 'app_template';

    /**
     * @return array<string, string>
     */
    public function getFiles(): array
    {
        return [
            self::COMPONENT_KEY_APP => 'App/Components/HTML/app.html',
        ];
    }

    #[\Override]
    protected function loadFiles(): void
    {
        parent::loadFiles();

        foreach ($this->getFiles() as $key => $file) {
            $path = new Path($file);
            $this->pathRegistry->set($key, $path);
            $this->loadFile($path);
        }
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
