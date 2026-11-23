<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\StringableModel;
use App\Models\User;
use App\Templates\HTML\HomeTemplate;
use App\Templates\HTML\UserTemplate;
use Core\Routing\Attributes\Route;
use Core\Routing\Controllers\AbstractController;
use Core\Routing\Models\Responses\Response;

final class MainController extends AbstractController
{
    public function __construct(
        private readonly HomeTemplate $homeTemplate,
        private readonly UserTemplate $userTemplate,
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

    #[Route(['/users'], 'app_users')]
    public function viewUsers(): Response
    {
        $users = [
            new User('user1', new \DateTimeImmutable('now')),
            new User('user2', new \DateTimeImmutable('now - 5 seconds')),
        ];

        $userComponent = $this->userTemplate->loadUserCards($users);

        return new Response($this->homeTemplate->loadApp('Users', $userComponent));
    }
}
