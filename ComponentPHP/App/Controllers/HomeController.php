<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Middleware\Admin;
use App\Middleware\Auth;
use App\Services\AuthService;
use App\Templates\HTML\RootTemplate;
use Core\Routing\Attributes\Route;
use Core\Routing\Controllers\AbstractController;
use Core\Routing\Models\Responses\Response;
use Core\Routing\Services\RoutingService;
use Core\Sessions\Services\SessionService;

#[Auth]
final class HomeController extends AbstractController
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly RootTemplate $rootTemplate,
        private readonly RoutingService $routingService,
    ) {
    }

    #[Route(['/', '/home'], 'app_home')]
    public function index(): Response
    {
        // TODO: All component work should solely live in templates I think

        $component = $this->rootTemplate
            ->get('app')
            ->fill('navigation', $this->rootTemplate->get('nav_bar'))
        ;

        $htmxRedirectData = SessionService::sessionPop('htmx');
        if ($htmxRedirectData !== null) {
            $component->fill('htmxBodyPreload', $htmxRedirectData, raw: true);
        }
        else {
            $component->fill('htmxBodyPreload', $this->routingService->tunnelRequest('app_viewAllSongs')->content, raw: true);
        }

        return new Response($component);
    }

    #[Admin]
    #[Route(['/admin'], 'app_admin')]
    public function admin(): Response
    {
        dump($this->authService->getUser());

        return new Response('Hi! (Admin)');
    }
}
