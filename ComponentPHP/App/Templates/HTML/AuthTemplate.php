<?php

declare(strict_types=1);

namespace App\Templates\HTML;

use Core\Components\Models\AbstractTemplate;

class AuthTemplate extends AbstractTemplate
{
    #[\Override]
    protected function init(): void
    {
        $this->loadFile('App/Components/auth.html');
    }
}
