<?php

declare(strict_types=1);

namespace Core\Components\Exceptions;

class NonExistentComponentException extends \Exception
{
    public function __construct(string $componentName, string $file)
    {
        parent::__construct("There is no component by the name '{$componentName}' in the file '{$file}'");
    }
}
