<?php

declare(strict_types=1);

namespace App\Middleware;

use Core\Middleware\Attributes\MiddlewareAttributeInterface;
use Core\Routing\Models\Request;
use Core\Routing\Models\Responses\RedirectResponse;
use Core\Routing\Models\Responses\Response;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
final class Partial implements MiddlewareAttributeInterface
{
    #[\Override]
    public function apply(Request $request): ?Response
    {
        if ($request->isHTMX) {
            return null;
        }

        return new RedirectResponse('/');
    }
}
