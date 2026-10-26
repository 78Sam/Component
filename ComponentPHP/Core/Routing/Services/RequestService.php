<?php

declare(strict_types=1);

namespace Core\Routing\Services;

use AbstractRequest;
use Core\Routing\Enums\RequestMethods;
use Core\Routing\Models\Requests\HTMXRequest;
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
        $route = $serverValidator->validate('REQUEST_URI', $serverValidator->validateString(...));
        $port = $serverValidator->validate('SERVER_PORT', $serverValidator->validateInt(...));
        $queryString = $serverValidator->validateWithDefault('QUERY_STRING', $serverValidator->validateString(...), '');
        $requestMethod = $serverValidator->validate('REQUEST_METHOD', $serverValidator->validateString(...));
        $requestTime = $serverValidator->validate('REQUEST_TIME', $serverValidator->validateInt(...));

        $request = new Request(
            host: $host,
            scheme: strtoupper($scheme),
            route: static::normaliseRoute($route),
            port: $port,
            queryString: $queryString,
            method: RequestMethods::from($requestMethod),
            requestTime: $requestTime,
            serverTime: time(),
            get: $_GET,
            post: $_POST,
            files: $_FILES,
            cookies: $_COOKIE,
            server: $_SERVER,
        );

        if (self::isHTMXRequest()) {
            return HTMXRequest::fromRequest($request);
        }

        return $request;
    }

    public static function normaliseRoute(string $route): string
    {
        return '/' . trim(PathService::normalisePath($route), '/');
    }

    private static function isHTMXRequest(): bool
    {
        if (array_key_exists('HTTP_HX_REQUEST', $_SERVER)) {
            return true;
        }

        if (array_key_exists('HTTP_HX_HISTORY_RESTORE_REQUEST', $_SERVER)) {
            return true;
        }
        
        return false;
    }
}
