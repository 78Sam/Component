<?php

declare(strict_types=1);

namespace Core\Routing\Enums;

enum RequestMethods: string
{
    case Get = 'GET';
    case Post = 'POST';
    case Put = 'PUT';
    case Patch = 'PATCH';
}
