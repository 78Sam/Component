<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use App\Templates\HTML\FormTemplate;
use App\Templates\HTML\RootTemplate;
use Core\Routing\Attributes\Route;
use Core\Routing\Controllers\AbstractController;
use Core\Routing\Models\Responses\RedirectResponse;
use Core\Routing\Models\Request;
use Core\Routing\Models\Responses\Response;
use Core\Sessions\Services\SessionService;

class AuthController extends AbstractController
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly RootTemplate $rootTemplate,
        private readonly FormTemplate $formTemplate,
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

        $comp = $this->rootTemplate
            ->get('app')
            ->fill('body', $this->formTemplate->getLoginForm())
        ;

        return new Response($comp);
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

        $comp = $this->rootTemplate
            ->get('app')
            ->fill('body', $this->formTemplate->getRegisterForm())
        ;

        return new Response($comp);
    }

    #[Route(['/logout'], 'app_logout')]
    public function logout(): Response
    {
        SessionService::destroySession();

        return new RedirectResponse('/login');
    }
}
