<?php

declare(strict_types=1);

namespace Core\Middleware\Attributes;

use Core\Routing\Models\Request;
use Core\Routing\Models\Response;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class TestMiddleware implements MiddlewareAttributeInterface
{
    #[\Override]
    public function apply(Request $request): ?Response
    {
        error_log('hehe hello');

        return new Response('Hijacked!');
    }
}
