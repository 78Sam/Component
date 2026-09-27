<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use App\Templates\HTML\AuthTemplate;
use App\Templates\HTML\RootTemplate;
use Core\Routing\Attributes\Route;
use Core\Routing\Controllers\AbstractController;
use Core\Routing\Models\RedirectResponse;
use Core\Routing\Models\Request;
use Core\Routing\Models\Response;
use Core\Sessions\Services\SessionService;

class AuthController extends AbstractController
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly RootTemplate $rootTemplate,
        private readonly AuthTemplate $authTemplate,
    ) {
    }

    #[Route(['/login'], 'app_login')]
    public function login(Request $request): Response
    {
        if ($request->method === "POST") {
            $user = $this->authService->login($request);
            if ($user !== null) {
                return new RedirectResponse('/');
            }
        }

        $page = $this->rootTemplate
            ->get('app')
            ->fill('body', $this->authTemplate->get('login_form'))
        ;

        return new Response($page);
    }

    #[Route(['/register'], 'app_register')]
    public function register(Request $request): Response
    {
        if ($request->method === "POST") {
            $user = $this->authService->register($request);
            if ($user !== null) {
                return new RedirectResponse('/');
            }
        }

        $page = $this->rootTemplate
            ->get('app')
            ->fill('body', $this->authTemplate->get('register_form'))
        ;

        return new Response($page);
    }

    #[Route(['/logout'], 'app_logout')]
    public function logout(): Response
    {
        SessionService::destroySession();

        return new RedirectResponse('/login');
    }
}
