<?php

declare(strict_types=1);

namespace Core\Routing;

use Core\Routing\Enums\ResponseCode;
use Core\Routing\Models\Request;
use Core\Routing\Models\Responses\Response;
use Core\Utility\Services\PathService;

class Router extends AbstractRouter
{
    #[\Override]
    protected function getControllerFolders(): array
    {
        return [
            PathService::fromProjectDirectory('App', 'Controllers'),
        ];
    }

    #[\Override]
    public function buildRequest(array $server, array $get, array $post, array $files, array $cookies): Request
    {
        throw new \Exception('Not implemented');
    }

    #[\Override]
    public function handleRequest(Request $request): Response
    {
        $siteMapEntry = $this->findRoute($request);
        if ($siteMapEntry === null) {
            return new Response('<h1>404</h1>', ResponseCode::NotFound);
        }
    }
}
