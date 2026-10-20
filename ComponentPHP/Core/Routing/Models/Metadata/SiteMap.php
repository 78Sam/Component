<?php

declare(strict_types=1);

namespace Core\Routing\Models\Metadata;

final class SiteMap
{
    /** @var array<string, list<SiteMapEntry>> */
    private array $siteMapEntriesByName = [];

    /** @var array<string, SiteMapEntry> */
    private array $siteMapEntriesByRoute = [];

    public function add(SiteMapEntry $siteMapEntry): self
    {
        if (array_key_exists($siteMapEntry->route, $this->siteMapEntriesByRoute)) {
            throw new \LogicException("Site map entry already exists for route: '{$siteMapEntry->route}'");
        }
        $this->siteMapEntriesByRoute[$siteMapEntry->route] = $siteMapEntry;

        if (!array_key_exists($siteMapEntry->name, $this->siteMapEntriesByName)) {
            $this->siteMapEntriesByName[$siteMapEntry->name] = [];
        }

        foreach ($this->siteMapEntriesByName[$siteMapEntry->name] as $existingEntry) {
            if ($siteMapEntry->method !== $existingEntry->method) {
                throw new \LogicException("Site map entry already with a different method exists for name: '{$siteMapEntry->name}'");
            }
        }
        $this->siteMapEntriesByName[$siteMapEntry->name][] = $siteMapEntry; 

        return $this;
    }

    /**
     * @return null|list<SiteMapEntry>
     */
    public function getSiteMapEntryByName(string $name): ?array
    {
        return $this->siteMapEntriesByName[$name] ?? null;
    }

    public function getSiteMapEntryByRoute(string $route): ?SiteMapEntry
    {
        return $this->siteMapEntriesByRoute[$route] ?? null;
    }

    /**
     * @return list<SiteMapEntry>
     */
    public function getDynamicSiteMapEntries(): array
    {
        return array_filter($this->siteMapEntriesByRoute, fn(SiteMapEntry $siteMapEntry): bool => $siteMapEntry->isDynamic());
    }
}
