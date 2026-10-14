<?php

declare(strict_types=1);

namespace Core\Routing\Enums;

enum ResponseCode: int
{
    case Ok = 200;

    case Found = 302;

    case Forbidden = 403;
    case NotFound = 404;
    case MethodNotAllowed = 405;

    case InternalServerError = 500;
}
