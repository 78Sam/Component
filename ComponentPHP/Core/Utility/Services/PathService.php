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
