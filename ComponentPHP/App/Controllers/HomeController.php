<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Middleware\Admin;
use App\Middleware\Auth;
use App\Services\AuthService;
use App\Services\MusicService;
use App\Templates\HTML\RootTemplate;
use Core\Routing\Attributes\Route;
use Core\Routing\Controllers\AbstractController;
use Core\Routing\Models\Request;
use Core\Routing\Models\Responses\Response;

#[Auth]
final class HomeController extends AbstractController
{
    public function __construct(
        public readonly AuthService $authService,
        public readonly RootTemplate $rootTemplate,
        public readonly MusicService $musicService,
    ) {
    }

    #[Route(['/'], 'app_home')]
    public function index(): Response
    {
        dump($this->authService->getUser());

        return new Response('Hi!');
    }

    #[Route(['/upload'], 'app_upload')]
    public function upload(Request $request): Response
    {
        if ($request->method === Request::METHOD_POST) {
            dump($request);
        }

        $form = $this->rootTemplate
            ->get('app')
            ->fill('body', $this->rootTemplate->get('upload_form'))
        ;

        return new Response($form);
    }

    #[Admin]
    #[Route(['/admin'], 'app_admin')]
    public function admin(): Response
    {
        dump($this->authService->getUser());

        return new Response('Hi! (Admin)');
    }
}
