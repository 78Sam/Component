<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use App\Templates\HTML\FormTemplate;
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

        return new Response($this->formTemplate->getLoginForm());
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

        return new Response($this->formTemplate->getRegisterForm());
    }

    #[Route(['/logout'], 'app_logout')]
    public function logout(): Response
    {
        SessionService::destroySession();

        return new RedirectResponse('/login');
    }
}
