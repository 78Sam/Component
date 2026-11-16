<?php

declare(strict_types=1);

namespace Core\Routing;

use Core\Routing\Models\Requests\Request;
use Core\Routing\Models\Responses\Response;

interface RouterInterface
{
    public function handleRequest(Request $request): Response;
}
