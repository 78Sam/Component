<?php

declare(strict_types=1);

namespace Core\Routing;

use Core\DependencyInjection\Services\ReflectionService;
use Core\Routing\Attributes\Route;
use Core\Routing\Controllers\AbstractController;
use Core\Routing\Models\Request;
use Core\Routing\Models\SiteMap;
use Core\Routing\Models\SiteMapEntry2;
use Core\Utility\Services\ClassFinderService;

abstract class AbstractRouter implements RouterInterface
{
    private static ?SiteMap $_siteMap = null;

    private SiteMap $siteMap {
        get => self::$_siteMap ??= $this->getSiteMap();
    }

    public function __construct(
        private readonly ClassFinderService $classFinderService,
    ) {
    }

    /**
     * @return list<string>
     */
    abstract protected function getControllerFolders(): array;

    protected function findRoute(Request $request): ?SiteMapEntry2
    {        
        $siteMapEntry = $this->siteMap->getSiteMapEntryByRoute($request->route);
        if ($siteMapEntry !== null) {
            return $siteMapEntry;
        }

        // Its likely that the request is for a dynamic route e.g. /user/{id:[0-9]+}/edit

        $dynamicSiteMapEntries = $this->siteMap->getDynamicSiteMapEntries();
        foreach ($dynamicSiteMapEntries as $dynamicSiteMapEntry) {
            // #'s to avoid the pain of escaping all the /'s
            if (preg_match("#^{$dynamicSiteMapEntry->route}$#", $request->route) === 1) {
                return $dynamicSiteMapEntry;
            }
        }

        return null;
    }

    private function getSiteMap(): SiteMap
    {
        $siteMap = new SiteMap();

        $controllers = [];
        foreach ($this->getControllerFolders() as $controllerFolder) {
            foreach ($this->classFinderService->byExtension($controllerFolder, AbstractController::class) as $controller) {
                $controllers[] = $controller;
            }
        }

        foreach ($controllers as $controller) {
            $attributes = ReflectionService::getAttributesInClass($controller, Route::class);

            foreach ($attributes as $attribute) {
                $routeInstance = $attribute->newInstance();

                foreach ($routeInstance->routes as $route) {
                    $siteMap->add(
                        new SiteMapEntry2(
                            $routeInstance->name,
                            $route,
                            $attribute,
                        ),
                    );
                }
            }
        }

        return $siteMap;
    }
}
