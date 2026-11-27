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
                ->get(DumpSchematic::C_DATA_ITEM, DumpSchematic::getPath())
                ->fill(DumpSchematic::SOCKETS[DumpSchematic::C_DUMP][''], var_export($value, true))
            ;
        }

        return $this
            ->get(DumpSchematic::C_DUMP, DumpSchematic::getPath())
            ->fill($dumpComponent::SOCKET_DEBUG_FRAME, "{$backtrace}")
            ->fill($dumpComponent::SOCKET_DATA, $this->collect($dataComponents, '<br>'))
        ;
    }
}
