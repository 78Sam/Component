<?php

declare(strict_types=1);

namespace Core;

use Core\Config\AbstractConfig;
use Core\DependencyInjection\Container;
use Core\Routing\Services\RoutingService;
use Core\Sessions\Services\SessionService;
use Core\Utility\Services\ClassFinderService;

class Kernel
{
    public readonly bool $isFranken;
    public readonly Container $container;
    public readonly RoutingService $routingService;
    public readonly ClassFinderService $classFinderService;

    /** @var list<AbstractConfig> */
    private array $configFiles = [];

    public function __construct(
        public readonly string $workerId,
    ) {
        $this->isFranken = ($_SERVER['SERVER_SOFTWARE'] ?? null) === 'FrankenPHP';
        $this->container = Container::getInstance();
        $this->routingService = $this->container->get(RoutingService::class);
        $this->classFinderService = $this->container->get(ClassFinderService::class);

        $configFileReflectionClasses = $this->classFinderService->byExtension('App/Config', AbstractConfig::class);
        foreach ($configFileReflectionClasses as $configFileReflectionClass) {
            $this->configFiles[] = $this->container->get($configFileReflectionClass->name);
        }
    }

    public function boot(): void
    {
        foreach ($this->configFiles as $configFile) {
            $configFile->onKernelBoot($this);
        }

        frankenphp_log('Booting kernel', context: ['workerId' => $this->workerId]);
    }

    public function handleRequest(array $server, array $get, array $post, array $files, array $cookies): void
    {
        $exception = null;
        try {
            SessionService::startSession();

            $request = $this->routingService->buildRequest($server, $get, $post, $files, $cookies);
            foreach ($this->configFiles as $configFile) {
                $configFile->preRequest($this, $request);
            }
            $response = $this->routingService->handleRequest($request);

            header("Content-Type: {$response->contentType}");
            foreach ($response->headers as $name => $value) {
                header("{$name}: {$value}");
            }
            http_response_code($response->responseCode);
            echo $response->content;
        }
        catch (\Throwable $th) {
            $exception = $th;
        }

        SessionService::closeSession();

        if ($exception !== null) {
            throw $exception;
        }
    }

    public function shutdown(): void
    {
        foreach ($this->configFiles as $configFile) {
            $configFile->preKernelShutdown($this);
        }

        frankenphp_log('Shutting down', context: ['workerId' => $this->workerId]);
    }
}
