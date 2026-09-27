<?php

declare(strict_types=1);

namespace Core;

use Core\Debug\DebugMetrics;
use Core\Routing\Router;
use Core\Sessions\Services\SessionService;

class Kernel
{
    public readonly Router $router;
    public readonly bool $isFranken;

    public function __construct(
        public readonly string $workerId,
    ) {
        $this->isFranken = ($_SERVER['SERVER_SOFTWARE'] ?? null) === 'FrankenPHP';
        $this->router = new Router();
    }

    public function boot(): void
    {
        frankenphp_log('Booting kernel', context: ['workerId' => $this->workerId]);
    }

    public function handleRequest(array $server, array $get, array $post, array $files, array $cookies): void
    {
        SessionService::startSession();

        $request = $this->router->buildRequest($server, $get, $post, $files, $cookies);
        $response = $this->router->handleRequest($request);

        SessionService::closeSession();

        foreach ($response->headers as $name => $value) {
            header("{$name}: {$value}");
        }
        http_response_code($response->responseCode);
        echo $response->content;
    }

    public function shutdown(): void
    {
        frankenphp_log('Shutting down', context: ['workerId' => $this->workerId]);
    }
}
