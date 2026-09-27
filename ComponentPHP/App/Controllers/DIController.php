<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\TestService;
use Core\DependencyInjection\Container;
use Core\Routing\Attributes\Route;
use Core\Routing\Controllers\AbstractController;
use Core\Routing\Models\Response;

class DIController extends AbstractController
{
    public function __construct(
        public readonly TestService $testService,
    ) {
    }

    #[Route(['/di'], 'app_di')]
    public function test(): Response
    {
        dump(Container::getInstance());

        return new Response('hey' . $this->testService->getValue());
    }
}
