<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Services\AuthService;
use Core\Middleware\Attributes\MiddlewareAttributeInterface;
use Core\Routing\Models\Request;
use Core\Routing\Models\Responses\Response;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Auth implements MiddlewareAttributeInterface
{
    #[\Override]
    public function apply(Request $request): ?Response
    {
        if (AuthService::isAuthenticated()) {
            return null;
        }

        return new Response('', 302, headers: [
            'Location' => '/login'
        ]);
    }
}
