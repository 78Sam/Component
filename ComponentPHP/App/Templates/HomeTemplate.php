<?php

declare(strict_types=1);

namespace App\Templates;

use Core\Components\Models\Components\ComponentInterface;
use Core\Components\Services\ComponentService;
use Core\Components\Templates\AbstractTemplate;
use Core\Utility\Paths\Models\Path;
use Core\Utility\Paths\Models\PathRegistry;

class HomeTemplate extends AbstractTemplate
{
    public const string COMPONENT_KEY_APP = 'app';

    public const array COMPONENT_KEY_PATH_MAPPINGS = [
        self::COMPONENT_KEY_APP => 'App/Components/app.html',
    ];

    public function __construct(
        ComponentService $componentService,
        private readonly PathRegistry $pathRegistry,
    ) {
        parent::__construct($componentService);
    }

    #[\Override]
    protected function loadFiles(): void
    {
        $this->pathRegistry->set(self::COMPONENT_KEY_APP, new Path(self::COMPONENT_KEY_PATH_MAPPINGS[self::COMPONENT_KEY_APP]));

        $this->loadFile($this->pathRegistry->get(self::COMPONENT_KEY_APP));
    }

    public function loadApp(string $title, string $body): ComponentInterface
    {
        return $this
            ->get(self::COMPONENT_KEY_APP, $this->pathRegistry->get(self::COMPONENT_KEY_APP))
            ->fill('title', $title)
            ->fill('body', $body)
        ;
    }
}
