<?php

declare(strict_types=1);

namespace Core\Site\Templates;

use Core\Components\Models\Components\ComponentInterface;
use Core\Components\Templates\AbstractTemplate;
use Core\Debug\Models\Backtrace;
use Core\Site\Generated\Schematics\DumpSchematic;

class DebugTemplate extends AbstractTemplate
{
    #[\Override]
    protected function setup(): void
    {
        $this->loadFile(DumpSchematic::getPath());
    }

    public function getDump(Backtrace $backtrace, mixed ...$values): ComponentInterface
    {
        $dataComponents = [];
        foreach ($values as $value) {
            $dataComponents[] = $this
                ->get(DumpSchematic::COMPONENT_DATA_ITEM, DumpSchematic::getPath())
                ->fill('data', var_export($value, true))
            ;
        }

        return $this
            ->get(DumpSchematic::COMPONENT_DUMP, DumpSchematic::getPath())
            ->fill('debugFrame', "{$backtrace}")
            ->fill('data', $this->collect($dataComponents, '<br>'))
        ;
    }
}
