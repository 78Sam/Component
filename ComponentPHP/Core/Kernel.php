<?php

declare(strict_types=1);

namespace Core;

use Core\Routing\Services\RoutingService;
use Core\Sessions\Services\SessionService;

class Kernel
{
    public readonly RoutingService $routingService;
    public readonly bool $isFranken;

    public function __construct(
        public readonly string $workerId,
    ) {
        $this->isFranken = ($_SERVER['SERVER_SOFTWARE'] ?? null) === 'FrankenPHP';
        $this->routingService = new RoutingService();
    }

    public function boot(): void
    {
        frankenphp_log('Booting kernel', context: ['workerId' => $this->workerId]);
    }

    public function handleRequest(array $server, array $get, array $post, array $files, array $cookies): void
    {
        SessionService::startSession();

        $request = $this->routingService->buildRequest($server, $get, $post, $files, $cookies);
        $response = $this->routingService->handleRequest($request);

        SessionService::closeSession();

        header("Content-Type: {$response->contentType}");
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
