<?php

declare(strict_types=1);

namespace Core\Routing\Models;

class SiteMap
{
    /** @var array<string, SiteMapEntry2> */
    private array $siteMapEntriesByName = [];

    /** @var array<string, SiteMapEntry2> */
    private array $siteMapEntriesByRoute = [];

    public function add(SiteMapEntry2 $siteMapEntry2): self
    {
        if (array_key_exists($siteMapEntry2->name, $this->siteMapEntriesByName)) {
            throw new \Exception("Site map entry already exists for name: '{$siteMapEntry2->name}'");
        }

        if (array_key_exists($siteMapEntry2->route, $this->siteMapEntriesByRoute)) {
            throw new \Exception("Site map entry already exists for route: '{$siteMapEntry2->route}'");
        }

        $this->siteMapEntriesByName[$siteMapEntry2->name] = $siteMapEntry2; 
        $this->siteMapEntriesByRoute[$siteMapEntry2->route] = $siteMapEntry2; 

        return $this;
    }

    public function getSiteMapEntryByName(string $name): ?SiteMapEntry2
    {
        return $this->siteMapEntriesByName[$name] ?? null;
    }

    public function getSiteMapEntryByRoute(string $route): ?SiteMapEntry2
    {
        return $this->siteMapEntriesByRoute[$route] ?? null;
    }

    /**
     * @return list<SiteMapEntry2>
     */
    public function getDynamicSiteMapEntries(): array
    {
        return array_filter($this->siteMapEntriesByName, fn(SiteMapEntry2 $siteMapEntry): bool => $siteMapEntry->isDynamic);
    }
}
