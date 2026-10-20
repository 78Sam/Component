<?php

declare(strict_types=1);

namespace Core;

use Core\Config\AbstractConfig;
use Core\DependencyInjection\Container;
use Core\Logging\Services\LoggingService;
use Core\Routing\Router;
use Core\Routing\RouterInterface;
use Core\Sessions\Services\SessionService;
use Core\Utility\Services\ClassFinderService;
use Core\Utility\Services\PathService;

class Kernel
{
    public readonly bool $isFranken;
    public readonly Container $container;

    private readonly RouterInterface $routingService;
    private readonly ClassFinderService $classFinderService;
    private readonly LoggingService $loggingService;

    /** @var list<AbstractConfig> */
    private array $configFiles = [];

    public function __construct(
        public readonly string $workerId,
    ) {
        $this->isFranken = ($_SERVER['SERVER_SOFTWARE'] ?? null) === 'FrankenPHP';
        $this->container = Container::getInstance();
        $this->routingService = $this->container->get(Router::class);
        $this->classFinderService = $this->container->get(ClassFinderService::class);
        $this->loggingService = $this->container->get(LoggingService::class);

        $configFileReflectionClasses = $this->classFinderService->byExtension(
            PathService::fromProjectDirectory('App', 'Config'),
            AbstractConfig::class,
        );
        foreach ($configFileReflectionClasses as $configFileReflectionClass) {
            $this->configFiles[] = $this->container->get($configFileReflectionClass->name);
        }
    }

    public function boot(): void
    {
        foreach ($this->configFiles as $configFile) {
            $configFile->onKernelBoot($this);
        }
        $this->loggingService->log("Booting kernel ({$this->workerId})");
    }

    public function handleRequest(array $server, array $get, array $post, array $files, array $cookies): void
    {
        $this->container->clearCache();

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
            http_response_code($response->responseCode->value);
            echo $response->content;
        } catch (\Throwable $th) {
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
        $this->loggingService->log("Shutting down ({$this->workerId})");
        $this->loggingService->writeLogs(PathService::fromProjectDirectory('Logs', 'log.log'));
    }
}
