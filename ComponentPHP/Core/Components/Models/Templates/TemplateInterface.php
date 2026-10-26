<?php

declare(strict_types=1);

namespace Core\Components\Models\Templates;

use Core\Components\Models\Components\ComponentInterface;

interface TemplateInterface
{
    public function loadFile(string $path, bool $absolutePath = false): self;

    public function get(string $name, string $path, string $model, bool $absolutePath = false): ComponentInterface;
}
