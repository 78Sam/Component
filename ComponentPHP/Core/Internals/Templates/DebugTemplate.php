<?php

declare(strict_types=1);

namespace Core\Internals\Templates;

use Core\Components\Models\Components\ComponentInterface;
use Core\Components\Templates\AbstractTemplate;
use Core\Debug\Models\Backtrace;
use Core\Internals\Generated\Schematics\DumpSchematic;

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
                ->get(DumpSchematic::DATA_ITEM, DumpSchematic::getPath())
                ->fill('data', var_export($value, true))
            ;
        }

        return $this
            ->get(DumpSchematic::DUMP, DumpSchematic::getPath())
            ->fill('debugFrame', "{$backtrace}")
            ->fill('data', $this->collect($dataComponents, '<br>'))
        ;
    }
}
