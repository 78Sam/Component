<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Middleware\Admin;
use App\Middleware\Auth;
use App\Services\AuthService;
use App\Services\MusicService;
use App\Templates\HTML\MusicTemplate;
use App\Templates\HTML\RootTemplate;
use Core\Routing\Attributes\Route;
use Core\Routing\Controllers\AbstractController;
use Core\Routing\Models\Request;
use Core\Routing\Models\Responses\Response;
use Core\Sessions\Services\SessionService;

#[Auth]
final class HomeController extends AbstractController
{
    public function __construct(
        public readonly AuthService $authService,
        public readonly RootTemplate $rootTemplate,
        public readonly MusicTemplate $musicTemplate,
        public readonly MusicService $musicService,
    ) {
    }

    #[Route(['/'], 'app_home')]
    public function index(Request $request): Response
    {
        $component = $this->rootTemplate
            ->get('app')
            ->fill('body', $this->rootTemplate->get('nav_bar'))
        ;

        $htmxRedirectData = SessionService::sessionPop('htmx');
        if ($htmxRedirectData !== null) {
            $component->fill('htmxBodyPreload', $htmxRedirectData, raw: true);
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
