<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Templates\HomeTemplate;
use Core\Routing\Attributes\Route;
use Core\Routing\Controllers\AbstractController;
use Core\Routing\Models\Responses\Response;

final class MainController extends AbstractController
{
    public function __construct(
        private readonly HomeTemplate $homeTemplate,
    ) {
    }

    #[Route(['/', '/home'], 'app_home')]
    public function home(): Response
    {
        return new Response($this->homeTemplate->loadApp('Home', 'Welcome!'));
    }
}
