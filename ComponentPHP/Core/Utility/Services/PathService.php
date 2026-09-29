<?php

declare(strict_types=1);

namespace Core\Utility\Services;

final class PathService
{
    // TODO: Test suite

    public static function getProjectDirectory(): string
    {
        return static::normalisePath(dirname(__DIR__, 3));
    }

    public static function normalisePath(string $path): string
    {
        $path = preg_replace('/\/+/', '/', str_replace('\\', '/', $path));
        $startsWithSlash = $path[0] === '/';
        $path = trim($path, '/');

        return $startsWithSlash ? "/{$path}" : $path;
    }

    public static function combineSegments(string ...$segments): string
    {
        return static::normalisePath(implode('/', $segments));
    }

    public static function fromProjectDirectory(string ...$segments): string
    {
        return static::normalisePath(static::getProjectDirectory() . '/' . implode('/', $segments));
    }
}

// echo PathService::getProjectDirectory() . PHP_EOL;
// echo PathService::normalisePath('\\\///awd/awds//\as') . PHP_EOL;
// echo PathService::combineSegments('\\\///awd/awds//\as', 'a', 'a', '\\\\b//b/') . PHP_EOL;
// echo PathService::combineSegments('\\C:/', 'awd/awds//\as', 'a', 'a', '\\\\b//b/') . PHP_EOL;
// echo PathService::fromProjectDirectory('\\\///awd/awds//\as', 'a', 'a', '\\\\b//b/') . PHP_EOL;
