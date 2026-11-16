<?php

declare(strict_types=1);

namespace Core\Routing;

use Core\DependencyInjection\Container;
use Core\DependencyInjection\Services\ReflectionService;
use Core\Routing\Enums\ResponseCode;
use Core\Routing\Models\Requests\Request;
use Core\Routing\Models\Responses\Response;
use Core\Utility\Paths\Services\PathService;

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
    public function handleRequest(Request $request): Response
    {
        $siteMapEntry = $this->findRoute($request);
        if ($siteMapEntry === null) {
            return new Response('<h1>404</h1>', ResponseCode::NotFound);
        }

        $route = $siteMapEntry->routeAttribute->newInstance();
        if ($route->HTTPVerbs !== [] && !in_array($request->method, $route->HTTPVerbs, true)) {
            return new Response('<h1>405</h1>', ResponseCode::MethodNotAllowed);
        }

        $middlewareResponse = $this->runMiddleware($siteMapEntry, $request);
        if ($middlewareResponse !== null) {
            return $middlewareResponse;
        }

        $dynamicParameters = $this->buildDynamicRequest($siteMapEntry, $request);

        // Add the request if its needed

        foreach ($siteMapEntry->method->getParameters() as $parameter) {
            $types = ReflectionService::getTypes($parameter);
            if (count($types) !== 1) {
                continue;
            }

            if (array_first($types) === Request::class) {
                $dynamicParameters[$parameter->name] = $request;

                break;
            }
        }

        $container = Container::getInstance();

        $methodParameters = $container->buildMethodParameters($siteMapEntry->method, $dynamicParameters);

        $response = $siteMapEntry->method->invoke(
            $container->get($siteMapEntry->controller->name),
            ...$methodParameters,
        );
        if (!$response instanceof Response) {
            throw new \LogicException("Controller method '{$siteMapEntry->method->name}' must return a Response");
        }

        return $response;
    }

    // public function tunnelRequest(string $path): Response
    // {
    // }
}
