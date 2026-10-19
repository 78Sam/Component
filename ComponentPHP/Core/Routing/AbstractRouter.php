<?php

declare(strict_types=1);

namespace Core\Routing;

use Core\Routing\Models\SiteMap;

abstract class AbstractRouter implements RouterInterface
{
    protected function getSiteMap(): SiteMap
    {

    }
}
