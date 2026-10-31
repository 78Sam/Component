<?php

declare(strict_types=1);

namespace Core\Language\Exceptions\Components;

class NestedComponentException extends \Exception
{
    public function __construct()
    {
        parent::__construct("You cannot open another component before closing the first");
    }
}
