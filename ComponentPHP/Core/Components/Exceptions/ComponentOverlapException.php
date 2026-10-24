<?php

declare(strict_types=1);

namespace Core\Components\Exceptions;

class ComponentOverlapException extends \Exception
{
    public function __construct(string $componentName, string $file)
    {
        parent::__construct("There are more than one components called '{$componentName}' in the file '{$file}'");
    }
}
