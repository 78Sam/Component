<?php

declare(strict_types=1);

namespace Core\Routing;

use Core\DependencyInjection\Container;
use Core\DependencyInjection\Services\ReflectionService;
use Core\Routing\Enums\RequestMethods;
use Core\Routing\Enums\ResponseCode;
use Core\Routing\Models\Requests\HTMXRequest;
use Core\Routing\Models\Requests\Request;
use Core\Routing\Models\Responses\Response;
use Core\Utility\Services\PathService;
use Core\Utility\Validators\Exceptions\ValidationException;
use Core\Utility\Validators\Services\ValidatorService;
use Core\Utility\Validators\Types\IntOrStringIntValidator;
use Core\Utility\Validators\Types\StringValidator;

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
            route: '/' . trim(parse_url($requirements['REQUEST_URI']->getValue(), PHP_URL_PATH), '/'),
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
}
