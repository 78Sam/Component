<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Middleware\Auth;
use Core\Routing\Attributes\Route;
use Core\Routing\Controllers\AbstractController;
use Core\Routing\Models\Request;
use Core\Routing\Models\Responses\JsonResponse;
use Core\Routing\Models\Responses\Response;

class RouteParameters extends AbstractController
{
    #[Route(['/param/{intval:[0-9]+}'], name: 'app_param')]
    public function getMe(int $intval, Request $request): Response
    {
        dump($intval);
        dump($request);

        return new Response("Value is '{$intval}'");
    }

    #[Auth]
    #[Route(['/json'], name: 'app_json')]
    public function jsonResponse(): Response
    {
        return new JsonResponse([
            'x' => [
                'y' => 10,
            ],
        ]);
    }
}
