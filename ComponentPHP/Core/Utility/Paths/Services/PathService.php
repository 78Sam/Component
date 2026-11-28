<?php

declare(strict_types=1);

namespace Core\Utility\Paths\Services;

final class PathService
{
    // TODO: Test suite

    public static function getProjectDirectory(): string
    {
        return self::normalisePath(dirname(__DIR__, 4));
    }

    /**
     * Turn an absolute path into one relative to the project directory
     */
    public static function makeRelative(string $path): string
    {
        return substr(self::normalisePath($path), strlen(self::getProjectDirectory()) + 1);
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
        return self::normalisePath(implode('/', $segments));
    }

    public static function fromProjectDirectory(string ...$segments): string
    {
        return self::normalisePath(self::getProjectDirectory() . '/' . implode('/', $segments));
    }

    /**
     * @return list<string>
     */
    public static function splitRoute(string $route): array
    {
        return array_values(array_filter(explode('/', $route), fn(string $value): bool => $value !== ''));
    }

    public static function removeExtension(\SplFileInfo $file): string
    {
        $filename = $file->getFilename();

        return substr($filename, 0, strlen($filename) - strlen(".{$file->getExtension()}"));
    }
}
