<?php

declare(strict_types=1);

namespace Core\Routing\Models;

class SiteMap
{
    private readonly array $siteMapEntriesByRoute;
    private readonly array $siteMapEntriesByRouteName;

    /**
     * @param list<SiteMapEntry> $siteMapEntries
     */
    public function __construct(array $siteMapEntries)
    {
        $siteMapEntriesByRoute = [];
        $siteMapEntriesByRouteName = [];

        foreach ($siteMapEntries as $siteMapEntry) {
            $routeAttribute = $siteMapEntry->route;
            foreach ($routeAttribute->routes as $route) {
                $siteMapEntriesByRoute[$route] = $siteMapEntry;
            }
            $siteMapEntriesByRouteName[$routeAttribute->name] = $siteMapEntry;
        }

        $this->siteMapEntriesByRoute = $siteMapEntriesByRoute;
        $this->siteMapEntriesByRouteName = $siteMapEntriesByRouteName;
    }

    public function getEntryFromRequest(Request $request): ?SiteMapEntry
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
}
