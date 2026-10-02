<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Models\User;
use App\Services\AuthService;
use Core\Middleware\Attributes\MiddlewareAttributeInterface;
use Core\Routing\Models\Request;
use Core\Routing\Models\Responses\Response;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
final class Admin implements MiddlewareAttributeInterface
{
    public function __construct(
        public readonly AuthService $authService,
    ) {
    }

    #[\Override]
    public function apply(Request $request): ?Response
    {
        if ($this->authService->getUser()?->role === User::ROLE_ADMIN) {
            return null;
        }

        return new Response('', 302, headers: [
            'Location' => '/'
        ]);
    }
}
