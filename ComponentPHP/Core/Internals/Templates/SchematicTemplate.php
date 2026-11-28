<?php

declare(strict_types=1);

namespace Core\Internals\Templates;

use Core\Components\Models\Components\ComponentInterface;
use Core\Components\Templates\AbstractTemplate;
use Core\Internals\Generated\Schematics\SchematicSchematic;
use Core\Utility\Paths\Models\Path;
use Core\Utility\Paths\Services\PathService;
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
                ->get(SchematicSchematic::_COMPONENT_CONSTANT, SchematicSchematic::getPath())
                ->fill('constantName', FormattingService::underscoreToScreamingSnakeCase($componentName))
                ->fill('name', FormattingService::underscoreToCamelCase($componentName))
            ;
        }

        $filename = $path->getFilename();

        return $this
            ->get(SchematicSchematic::_CLASS, SchematicSchematic::getPath())
            ->fill('componentConstants', $this->collect($constants))
            ->fill('className', FormattingService::underscoreToPascalCase($filename))
            ->fill('path', PathService::makeRelative($path->getPath()))
        ;
    }
}
