<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\Recurse1;
use App\Services\TestService;
use Core\DependencyInjection\Container;
use Core\Routing\Attributes\Route;
use Core\Routing\Controllers\AbstractController;
use Core\Routing\Models\Responses\Response;

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

    #[Route(['/di2'], 'app_di')]
    public function test2(Recurse1 $recurse1): Response
    {
        return new Response('hey' . $this->testService->getValue());
    }
}
