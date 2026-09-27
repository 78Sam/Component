<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Components\Services\ComponentService;
use Core\Middleware\Attributes\TestMiddleware;
use Core\Routing\Attributes\Route;
use Core\Routing\Controllers\AbstractController;
use Core\Routing\Models\Response;

#[TestMiddleware]
class TestController extends AbstractController
{
    private readonly ComponentService $componentService;

    public function __construct()
    {
        $this->componentService = new ComponentService();
    }

    #[Route(['/', '/index'], 'app_index')]
    public function index(): Response
    {
        $component = $this->componentService
            ->get('app', 'App/Components/app.html')
            ?->fill('name', 'Sam')
        ;

        return new Response($component ?? 'Unable to load component');
    }

    #[Route(['/test'], 'app_test')]
    public function test(): Response
    {
        error_log('made it');

        return new Response('Hello :)');
    }

    #[Route(['/redirect'], 'app_redirect')]
    public function redirect(): Response
    {
        return new Response('', 302, headers: [
            'Location' => '/',
        ]);
    }
}
