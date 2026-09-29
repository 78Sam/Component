<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Middleware\Admin;
use App\Middleware\Auth;
use App\Services\UserService;
use Core\Routing\Attributes\Route;
use Core\Routing\Controllers\AbstractController;
use Core\Routing\Models\Responses\Response;

#[Auth]
final class HomeController extends AbstractController
{
    public function __construct(
        public readonly UserService $userService,
    ) {
    }

    #[Route(['/'], 'app_home')]
    public function index(): Response
    {
        dump($this->userService->getUser());

        return new Response('Hi!');
    }

    #[Admin]
    #[Route(['/admin'], 'app_admin')]
    public function admin(): Response
    {
        dump($this->userService->getUser());

        return new Response('Hi! (Admin)');
    }
}
