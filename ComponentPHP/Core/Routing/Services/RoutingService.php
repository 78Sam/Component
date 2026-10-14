<?php

declare(strict_types=1);

namespace Core\Routing\Services;

use Core\DependencyInjection\Container;
use Core\Logging\Services\LoggingService;
use Core\Middleware\Attributes\MiddlewareAttributeInterface;
use Core\Routing\Attributes\Route;
use Core\Routing\Controllers\AbstractController;
use Core\Routing\Enums\RequestMethods;
use Core\Routing\Enums\ResponseCode;
use Core\Routing\Models\HTMXRequest;
use Core\Routing\Models\Request;
use Core\Routing\Models\Responses\Response;
use Core\Routing\Models\RouteSegment;
use Core\Routing\Models\SiteMapEntry;
use Core\Utility\Services\ClassFinderService;
use Core\Utility\Services\PathService;
use Core\Utility\Validators\Exceptions\ValidationException;
use Core\Utility\Validators\Services\ValidatorService;
use Core\Utility\Validators\Types\IntOrStringIntValidator;
use Core\Utility\Validators\Types\StringValidator;

final class RoutingService
{
    /** @var array<string, SiteMapEntry> */
    public array $staticSiteMapEntries = [];

    /** @var array<string, SiteMapEntry> */
    public array $dynamicSiteMapEntries = [];

    /** @var array<string, SiteMapEntry> */
    public array $routeNameToSiteMapEntry = [];

    /** @var array<string, AbstractController> */
    public array $cachedControllers = [];

    public function __construct(
        private readonly ClassFinderService $classFinderService,
        private readonly LoggingService $loggingService,
    ) {
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

        $request = new Request(
            host: $requirements['SERVER_NAME']->getValue(),
            scheme: strtoupper($requirements['REQUEST_SCHEME']->getValueWithDefault($httpsScheme)),
            path: '/' . trim(parse_url($requirements['REQUEST_URI']->getValue(), PHP_URL_PATH), '/'),
            port: $requirements['SERVER_PORT']->getValue(),
            queryString: $requirements['QUERY_STRING']->getValueWithDefault(''),
            method: RequestMethods::from(strtoupper($requirements['REQUEST_METHOD']->getValue())),
            requestTime: $requirements['REQUEST_TIME']->getValue(),
            serverTime: time(),
            get: $get,
            post: $post,
            files: $files,
            cookies: $cookies,
            server: $server,
        );

        $htmxRequest = HTMXRequest::fromRequest($request);

        return $htmxRequest !== null ? $htmxRequest : $request;
    }

    public function handleRequest(Request $request): Response
    {
        $siteMapEntry = $this->matchSiteMapEntry($request);
        if ($siteMapEntry === null) {
            return new Response('<h1>404</h1>', ResponseCode::NotFound);
        }

        $route = $siteMapEntry->route;
        if ($route->HTTPVerbs !== [] && !in_array($request->method, $route->HTTPVerbs, true)) {
            return new Response('<h1>405</h1>', ResponseCode::MethodNotAllowed);
        }

        $this->loggingService->log("Routing '{$request->path}'");

        // If its a dynamic route match the route segments to the method arguments

        $dynamicParameters = [];
        if ($siteMapEntry->isDynamic()) {
            $explodedRoute = array_values(array_filter(
                explode('/', $request->path),
                fn(string $value): bool => $value !== '',
            ));

            foreach ($siteMapEntry->segments as $index => $segment) {
                if (!$segment->regex) {
                    continue;
                }

                $dynamicParameters[$segment->variable] = $explodedRoute[$index];
            }
        }

        foreach ($siteMapEntry->method->getParameters() as $parameter) {
            if (Container::getType($parameter) === Request::class) {
                $dynamicParameters[$parameter->name] = $request;
            }
        }
        $methodParameters = Container::getInstance()->buildMethodParameters($siteMapEntry->method, $dynamicParameters);

        // Run middleware

        $middlewareResult = $this->runMiddleware($siteMapEntry, $request);
        if ($middlewareResult !== null) {
            return $middlewareResult;
        }

        // Create and cache the controller instance

        if (!array_key_exists($request->path, $this->cachedControllers)) {
            $this->cachedControllers[$request->path] = Container::getInstance()->get($siteMapEntry->class->name);
        }
        $controller = $this->cachedControllers[$request->path];

        // Send request

        $response = $siteMapEntry->method->invoke($controller, ...$methodParameters);
        if (!$response instanceof Response) {
            throw new \LogicException("Controller method '{$siteMapEntry->method->name}' must return a Response");
        }

        return $response;
    }

    /**
     * @param array<string, mixed> $methodParameters
     */
    public function tunnelRequest(string $routeName, array $methodParameters = []): Response
    {
        // TODO: Tbh this should probably take an actual Request object
        if (!array_key_exists($routeName, $this->routeNameToSiteMapEntry)) {
            throw new \Exception("Unknown route name when tunnelling request: '{$routeName}'");
        }

        $siteMapEntry = $this->routeNameToSiteMapEntry[$routeName];
        $controller = Container::getInstance()->get($siteMapEntry->class->name);

        $response = $siteMapEntry->method->invoke($controller, ...$methodParameters);
        if (!$response instanceof Response) {
            throw new \LogicException("Controller method '{$siteMapEntry->method->name}' must return a Response");
        }

        return $response;
    }

    private function matchSiteMapEntry(Request $request): ?SiteMapEntry
    {
        // Static routes

        if (array_key_exists($request->path, $this->staticSiteMapEntries)) {
            return $this->staticSiteMapEntries[$request->path];
        }

        // Dynamic routes

        foreach ($this->dynamicSiteMapEntries as $pattern => $entry) {
            if (preg_match("/^{$pattern}$/", $request->path) === 1) {
                return $entry;
            }
        }

        return null;
    }

    private function runMiddleware(SiteMapEntry $siteMapEntry, Request $request): ?Response
    {
        $applyMiddleware = static function(\ReflectionClass|\ReflectionMethod $middlewareTarget, Request $request): ?Response {
            /** @var list<\ReflectionAttribute<MiddlewareAttributeInterface>> $middlewareAttributes */
            $middlewareAttributes = $middlewareTarget->getAttributes(
                MiddlewareAttributeInterface::class,
                \ReflectionAttribute::IS_INSTANCEOF,
            );

            foreach ($middlewareAttributes as $methodMiddlewareAttribute) {
                /** @var MiddlewareAttributeInterface $methodMiddlewareAttributeInstance */
                $methodMiddlewareAttributeInstance = Container::getInstance()->get($methodMiddlewareAttribute->name);
                $middlewareResult = $methodMiddlewareAttributeInstance->apply($request);
                if ($middlewareResult !== null) {
                    return $middlewareResult;
                }
            }

            return null;
        };

        $classMiddlewareResponse = $applyMiddleware($siteMapEntry->class, $request);
        if ($classMiddlewareResponse !== null) {
            return $classMiddlewareResponse;
        }

        $methodMiddlewareResponse = $applyMiddleware($siteMapEntry->method, $request);
        if ($methodMiddlewareResponse !== null) {
            return $methodMiddlewareResponse;
        }

        return null;
    }

    private function createSiteMap(): void
    {
        $controllers = $this->classFinderService->byExtension(
            PathService::fromProjectDirectory('App', 'Controllers'),
            AbstractController::class,
        );
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
                    if (array_key_exists($route, $this->staticSiteMapEntries)) {
                        throw new \LogicException("Static route already registered '{$route}'");
                    }

                    if (array_key_exists($route, $this->dynamicSiteMapEntries)) {
                        throw new \LogicException("Dynamic route already registered '{$route}'");
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
                    $this->routeNameToSiteMapEntry[$routeAttribute->name] = $siteMapEntry;

                    if ($staticRoute) {
                        $this->staticSiteMapEntries['/' . implode('/', $routeSegments)] = $siteMapEntry;

                        continue;
                    }

                    $this->dynamicSiteMapEntries['\/' . implode('\/', $routeSegments)] = $siteMapEntry;
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
                $segments[] = new RouteSegment($matches['pattern'] ?? '[^\\/]+', $matches['variable'], true);

                continue;
            }

            throw new \Exception("Failed to parse route segment '{$routeSegment}'");
        }

        return $segments;
    }
}
