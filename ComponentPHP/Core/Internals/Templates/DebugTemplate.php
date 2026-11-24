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
        $dataItemComponent = DumpSchematic::cDataItem();
        $dataComponents = [];
        foreach ($values as $value) {
            $dataComponents[] = $this
                ->get($dataItemComponent::getName(), DumpSchematic::getPath())
                ->fill($dataItemComponent::SOCKET_DATA, var_export($value, true))
            ;
        }

        $dumpComponent = DumpSchematic::cDump();

        return $this
            ->get($dumpComponent::getName(), DumpSchematic::getPath())
            ->fill($dumpComponent::SOCKET_DEBUG_FRAME, "{$backtrace}")
            ->fill($dumpComponent::SOCKET_DATA, $this->collect($dataComponents, '<br>'))
        ;
    }
}
