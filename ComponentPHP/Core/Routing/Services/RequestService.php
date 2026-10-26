<?php

declare(strict_types=1);

namespace Core\Routing\Services;

use AbstractRequest;
use Core\Routing\Models\Requests\Request;
use Core\Utility\Parsers\Implementations\SimpleParser;
use Core\Utility\Services\PathService;

class RequestService
{
    public static function buildRequestFromGlobals(): AbstractRequest
    {
        $serverValidator = new SimpleParser($_SERVER);

        $httpsScheme = $serverValidator->validateWithDefault('HTTPS', $serverValidator->validateString(...), 'off');
        $httpsScheme = in_array($httpsScheme, ['', 'off'], true) ? 'HTTP' : 'HTTPS';

        $host = $serverValidator->validate('SERVER_NAME', $serverValidator->validateString(...));
        $scheme = $serverValidator->validateWithDefault('REQUEST_SCHEME', $serverValidator->validateString(...), $httpsScheme);
        $route = static::normaliseRoute($serverValidator->validate('REQUEST_URI', $serverValidator->validateString(...)));

        // $request = new Request(
        //     host: $serverValidator->validate('SERVER_NAME', $serverValidator->validateString(...)),
        // );
    }

    public static function normaliseRoute(string $route): string
    {
        return '/' . trim(PathService::normalisePath($route), '/');
    }
}
