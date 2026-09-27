<?php

declare(strict_types=1);

namespace Core\Middleware\Attributes;

use Core\Routing\Models\Request;
use Core\Routing\Models\Response;

interface MiddlewareAttributeInterface
{
    public function apply(Request $request): ?Response;
}
