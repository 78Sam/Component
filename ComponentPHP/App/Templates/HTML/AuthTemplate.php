<?php

declare(strict_types=1);

namespace App\Templates\HTML;

use Core\Components\Models\AbstractTemplate;
use Core\Components\Services\ComponentService;

class AuthTemplate extends AbstractTemplate
{
    public function __construct(ComponentService $componentService)
    {
        parent::__construct($componentService);

        $this->loadFile('App/Components/auth.html');
    }
}
