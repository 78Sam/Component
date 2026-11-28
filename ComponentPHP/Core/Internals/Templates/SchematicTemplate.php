<?php

declare(strict_types=1);

namespace Core\Internals\Templates;

use Core\Components\Models\Components\ComponentInterface;
use Core\Components\Templates\AbstractTemplate;
use Core\Internals\Generated\Schematics\SchematicSchematic;
use Core\Utility\Paths\Models\Path;
use Core\Utility\Services\FormattingService;

class SchematicTemplate extends AbstractTemplate
{
    protected function setup(): void
    {
        $this->loadFile(SchematicSchematic::getPath());
    }

    /**
     * @param array<string, ComponentInterface> $components
     */
    public function getSchematic(array $components, Path $path): ComponentInterface
    {
        $constants = [];
        foreach ($components as $componentName => $component) {
            $constants[] = $this
                ->get(SchematicSchematic::COMPONENT_CONSTANT, SchematicSchematic::getPath())
                ->fill('constantName', FormattingService::underscoreToScreamingSnakeCase($componentName))
                ->fill('')
        }
    }
}
