<?php

declare(strict_types=1);

namespace Core\Routing\Services;

use Core\DependencyInjection\Container;
use Core\Middleware\Attributes\MiddlewareAttributeInterface;
use Core\Routing\Attributes\Route;
use Core\Routing\Controllers\AbstractController;
use Core\Routing\Models\Request;
use Core\Routing\Models\Responses\Response;
use Core\Routing\Models\RouteSegment;
use Core\Routing\Models\SiteMapEntry;
use Core\Utility\ClassFinder;
use Core\Utility\Validators\Exceptions\ValidationException;
use Core\Utility\Validators\Services\ValidatorService;
use Core\Utility\Validators\Types\IntOrStringIntValidator;
use Core\Utility\Validators\Types\StringValidator;

final class RoutingService
{
    private ClassFinder $classFinder;

    /** @var array<string, SiteMapEntry> */
    public array $siteMapEntries = [];

    /** @var array<string, SiteMapEntry> */
    public array $staticSiteMapEntries = [];

    /** @var array<string, SiteMapEntry> */
    public array $dynamicSiteMapEntries = [];

    /** @var array<string, AbstractController> */
    public array $cachedControllers = [];

    public function __construct()
    {
        $this->classFinder = new ClassFinder();
        $this->createSiteMap();
    }

    public function buildRequest(array $server, array $get, array $post, array $files, array $cookies): Request
    {
        $requirements = [
            'SERVER_NAME' => new StringValidator('SERVER_NAME'),
            'REQUEST_SCHEME' => new StringValidator('REQUEST_SCHEME'),
            'HTTPS' => new StringValidator('HTTPS'),
            'REQUEST_URI' => new StringValidator('REQUEST_URI'),
            'SERVER_PORT' => new IntOrStringIntValidator('SERVER_PORT'),
            'QUERY_STRING' => new StringValidator('QUERY_STRING'),
            'REQUEST_METHOD' => new StringValidator('REQUEST_METHOD'),
            'REQUEST_TIME' => new IntOrStringIntValidator('REQUEST_TIME'),
        ];

        ValidatorService::validate($requirements, $server);
        foreach ($requirements as $requirement) {
            $value = $requirement->getValueWithDefault();
            if ($value instanceof ValidationException) {
                throw $value;
            }
        }

        $httpsScheme = in_array($requirements['HTTPS']->getValueWithDefault(''), ['', 'off'], true) ? 'HTTP' : 'HTTPS';

        return new Request(
            host: $requirements['SERVER_NAME']->getValue(),
            scheme: strtoupper($requirements['REQUEST_SCHEME']->getValueWithDefault($httpsScheme)),
            path: '/' . trim(parse_url($requirements['REQUEST_URI']->getValue(), PHP_URL_PATH), '/'),
            port: $requirements['SERVER_PORT']->getValue(),
            queryString: $requirements['QUERY_STRING']->getValueWithDefault(''),
            method: $requirements['REQUEST_METHOD']->getValue(),
            requestTime: $requirements['REQUEST_TIME']->getValue(),
            serverTime: time(),
            get: $get,
            post: $post,
            files: $files,
            cookies: $cookies,
        );
    }

    public function handleRequest(Request $request): Response
    {
        // if (!array_key_exists($request->path, $this->siteMapEntries)) {
        //     return new Response('<h1>404</h1>', 404);
        // }

        // dump($this->staticSiteMapEntries);
        // dump($this->dynamicSiteMapEntries);

        $routeArguments = [];

        $siteMapEntry = null;
        if (array_key_exists($request->path, $this->staticSiteMapEntries)) {
            $siteMapEntry = $this->staticSiteMapEntries[$request->path];
        }

        if ($siteMapEntry === null) {
            foreach ($this->dynamicSiteMapEntries as $pattern => $entry) {
                $matches = [];
                if (preg_match("/^{$pattern}$/", $request->path, $matches) === 1) {
                    $siteMapEntry = $entry;
                    $explodedRoute = array_values(array_filter(explode('/', $request->path), fn(string $value): bool => $value !== ''));
                    foreach ($siteMapEntry->segments as $index => $segment) {
                        if (!$segment->regex) {
                            continue;
                        }
                        $routeArguments[$segment->variable] = $explodedRoute[$index];
                    }

                    break;
                }
            }
        }

        if ($siteMapEntry === null) {
            return new Response('<h1>404</h1>', 404);
        }

        // $siteMapEntry = $this->siteMapEntries[$request->path];

        $route = $siteMapEntry->route;
        if ($route->HTTPVerbs !== [] && !in_array($request->method, $route->HTTPVerbs)) {
            return new Response('<h1>404</h1>', 404);
        }

        // Run middleware

        $classMiddlewareResponse = $this->runMiddleware($siteMapEntry->class, $request);
        if ($classMiddlewareResponse !== null) {
            return $classMiddlewareResponse;
        }

        $methodMiddlewareResponse = $this->runMiddleware($siteMapEntry->method, $request);
        if ($methodMiddlewareResponse !== null) {
            return $methodMiddlewareResponse;
        }

        // Create and cache the controller instance

        if (!array_key_exists($request->path, $this->cachedControllers)) {
            // $this->cachedControllers[$request->path] = new $siteMapEntry->method->class();
            $this->cachedControllers[$request->path] = Container::getInstance()->get($siteMapEntry->class->name);
        }
        $controller = $this->cachedControllers[$request->path];

        // Send request

        $response = $siteMapEntry->method->invoke($controller, ...$routeArguments); // TODO: Add request back sometimes using reflection
        if (!$response instanceof Response) {
            throw new \LogicException("Controller method '{$siteMapEntry->method->name}' must return a Response");
        }

        return $response;
    }

    private function runMiddleware(\ReflectionClass|\ReflectionMethod $reflectionObject, Request $request): ?Response
    {
        /** @var list<\ReflectionAttribute<MiddlewareAttributeInterface>> $middlewareAttributes */
        $middlewareAttributes = $reflectionObject->getAttributes(
            MiddlewareAttributeInterface::class,
            \ReflectionAttribute::IS_INSTANCEOF,
        );

        foreach ($middlewareAttributes as $methodMiddlewareAttribute) {
            $methodMiddlewareAttributeInstance = $methodMiddlewareAttribute->newInstance();
            $middlewareResult = $methodMiddlewareAttributeInstance->apply($request);
            if ($middlewareResult !== null) {
                return $middlewareResult;
            }
        }

        return null;
    }

    private function createSiteMap(): void
    {
        $controllers = $this->classFinder->byExtension('App/Controllers', AbstractController::class);
        foreach ($controllers as $controller) {
            foreach ($controller->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $routeAttributes = $method->getAttributes(Route::class);
                $numRouteAttributes = count($routeAttributes);
                if ($numRouteAttributes === 0) {
                    continue;
                }

                if ($numRouteAttributes > 1) {
                    throw new \LogicException(
                        "Controller methods should only have 1 route attribute, '{$method->name}' has {$numRouteAttributes}",
                    );
                }

                $routeAttribute = $routeAttributes[0]->newInstance();
                $routes = $routeAttribute->routes;
                foreach ($routes as $route) {
                    if (array_key_exists($route, $this->siteMapEntries)) {
                        throw new \LogicException("Route already registered '{$route}'");
                    }

                    $routeSegments = $this->parseSegments($route);
                    $staticRoute = true;
                    foreach ($routeSegments as $routeSegment) {
                        if ($routeSegment->regex) {
                            $staticRoute = false;

                            break;
                        }
                    }

                    $siteMapEntry = new SiteMapEntry($routeAttribute, $controller, $method, $routeSegments);

                    if ($staticRoute) {
                        $this->staticSiteMapEntries['/' . implode('/', $routeSegments)] = $siteMapEntry;

                        continue;
                    }

                    $this->dynamicSiteMapEntries['\/' . implode('\/', $routeSegments)] = $siteMapEntry;

                    // $this->siteMapEntries[$route] = new SiteMapEntry($routeAttribute, $controller, $method, []);
                }
            }
        }
    }

    /**
     * @return list<RouteSegment>
     */
    private function parseSegments(string $route): array
    {
        $segments = [];
        foreach (explode('/', $route) as $routeSegment) {
            if ($routeSegment === '') {
                continue;
            }

            $matches = [];
            if (preg_match(RouteSegment::SEGMENT_PATTERN, $routeSegment, $matches) === 1) {
                $segments[] = new RouteSegment($matches['segment']);

                continue;
            }

            if (preg_match(RouteSegment::PATTERN_PATTERN, $routeSegment, $matches) === 1) {
                $segments[] = new RouteSegment(
                    $matches['pattern'] ?? '[^/]+',
                    $matches['variable'],
                    true,
                );

                continue;
            }

            throw new \Exception("Failed to parse route segment '{$routeSegment}'");
        }

        return $segments;
    }
}
