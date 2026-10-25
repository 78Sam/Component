<?php

declare(strict_types=1);

namespace Core\Routing;

use Core\DependencyInjection\Container;
use Core\DependencyInjection\Services\ReflectionService;
use Core\Logging\Services\LoggingService;
use Core\Middleware\Attributes\MiddlewareAttributeInterface;
use Core\Routing\Attributes\Route;
use Core\Routing\Controllers\AbstractController;
use Core\Routing\Models\Metadata\SiteMap;
use Core\Routing\Models\Metadata\SiteMapEntry;
use Core\Routing\Models\Requests\Request;
use Core\Routing\Models\Responses\Response;
use Core\Utility\Services\ClassFinderService;
use Core\Utility\Services\PathService;

abstract class AbstractRouter implements RouterInterface
{
    protected readonly SiteMap $siteMap;

    public function __construct(
        protected readonly LoggingService $loggingService,
        private readonly ClassFinderService $classFinderService,
    ) {
        $this->siteMap = $this->getSiteMap();
    }

    /**
     * @return list<string>
     */
    abstract protected function getControllerFolders(): array;

    protected function findRoute(Request $request): ?SiteMapEntry
    {
        $siteMapEntry = $this->siteMap->getSiteMapEntryByRoute($request->route);
        if ($siteMapEntry !== null) {
            return $siteMapEntry;
        }

        // Its likely that the request is for a dynamic route e.g. /user/{id:[0-9]+}/edit

        $dynamicSiteMapEntries = $this->siteMap->getDynamicSiteMapEntries();
        foreach ($dynamicSiteMapEntries as $dynamicSiteMapEntry) {
            // #'s to avoid the pain of escaping all the /'s
            if (preg_match($dynamicSiteMapEntry->asPattern(), $request->route) === 1) {
                return $dynamicSiteMapEntry;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildDynamicRequest(SiteMapEntry $siteMapEntry, Request $request): array
    {
        if (!$siteMapEntry->isDynamic()) {
            return [];
        }

        $explodedRoute = PathService::splitRoute($request->route);
        if (count($explodedRoute) !== count($siteMapEntry->routeSegments)) {
            throw new \LogicException(
                "Routes should match after \$this->findRoute(), '{$request->route}' vs '{$siteMapEntry->route}'",
            );
        }

        $dynamicParameters = [];
        foreach ($siteMapEntry->routeSegments as $index => $segment) {
            if (!$segment->isDynamic) {
                continue;
            }

            $dynamicParameters[$segment->variable] = $explodedRoute[$index];
        }

        return $dynamicParameters;
    }

    protected function runMiddleware(SiteMapEntry $siteMapEntry, Request $request): ?Response
    {
        $applyMiddleware = static function (
            \ReflectionClass|\ReflectionMethod $middlewareTarget,
            Request $request,
        ): ?Response {
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

        $classMiddlewareResponse = $applyMiddleware($siteMapEntry->controller, $request);
        if ($classMiddlewareResponse !== null) {
            return $classMiddlewareResponse;
        }

        $methodMiddlewareResponse = $applyMiddleware($siteMapEntry->method, $request);
        if ($methodMiddlewareResponse !== null) {
            return $methodMiddlewareResponse;
        }

        return null;
    }

    private function getSiteMap(): SiteMap
    {
        $this->loggingService->log('Building site map');

        $siteMap = new SiteMap();

        $controllers = [];
        foreach ($this->getControllerFolders() as $controllerFolder) {
            foreach ($this->classFinderService->byExtension(
                $controllerFolder,
                AbstractController::class,
            ) as $controller) {
                $controllers[] = $controller;
            }
        }

        foreach ($controllers as $controller) {
            $attributes = ReflectionService::getAttributesOnClassMethods($controller, Route::class);

            foreach ($attributes as $attribute) {
                $routeInstance = $attribute['attribute']->newInstance();

                foreach ($routeInstance->routes as $route) {
                    $siteMap->add(
                        new SiteMapEntry(
                            $routeInstance->name,
                            $route,
                            $attribute['attribute'],
                            $controller,
                            $attribute['method'],
                        ),
                    );
                }
            }
        }

        return $siteMap;
    }
}
