<?php

declare(strict_types=1);

namespace Core\Routing;

use Core\Routing\Models\Request;
use Core\Routing\Models\Responses\Response;

class Router extends AbstractRouter
{
    #[\Override]
    public function buildRequest(array $server, array $get, array $post, array $files, array $cookies): Request
    {
        throw new \Exception('Not implemented');
    }

    #[\Override]
    public function handleRequest(Request $request): Response
    {
        throw new \Exception('Not implemented');
    }
}
