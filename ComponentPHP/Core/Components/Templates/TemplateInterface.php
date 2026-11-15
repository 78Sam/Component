<?php

declare(strict_types=1);

namespace Core\Components\Templates;

use Core\Components\Models\Components\ComponentInterface;
use Core\Utility\Paths\Models\Path;

interface TemplateInterface
{
    public function loadFile(Path $path): self;

    public function get(string $name, Path $path): ComponentInterface;
}
