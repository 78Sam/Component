<?php

declare(strict_types=1);

namespace Tests\Core\Database;

use Core\Components\Services\ComponentService;
use Core\Database\Services\DatabaseService;
use Core\Testing\AbstractTest;

class DatabaseTests extends AbstractTest
{
    private ComponentService $componentService;
    private DatabaseService $database;

    #[\Override]
    public function setup(): void
    {
        $this->componentService = new ComponentService();
        $this->database = DatabaseService::getInstance();
    }
}
