<?php

declare(strict_types=1);

namespace Core\Middleware\Attributes;

use Core\Routing\Models\Requests\Request;
use Core\Routing\Models\Responses\Response;

interface MiddlewareAttributeInterface
{
    public function apply(Request $request): ?Response;
}
