<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Templates\HTML\HomeTemplate;
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

    #[Route(['/test'], 'app_test')]
    public function test(): Response
    {
        return new Response($this->homeTemplate->loadTest());
    }

//     public function viewUsers(): Response
//     {
//         $users = [
//             new User('user1', new \DateTimeImmutable('now')),
//             new User('user2', new \DateTimeImmutable('now - 5 seconds')),
//         ];
// 
//         return new Response($this->homeTemplate)
//     }
}
