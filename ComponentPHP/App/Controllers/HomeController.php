<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Middleware\Auth;
use Core\Routing\Attributes\Route;
use Core\Routing\Controllers\AbstractController;
use Core\Routing\Models\Responses\Response;

#[Auth]
final class HomeController extends AbstractController
{
    #[Route(['/'], 'app_home')]
    public function index(): Response
    {
        return new Response('Hi!');
    }
}
