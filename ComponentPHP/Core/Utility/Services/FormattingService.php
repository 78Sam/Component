<?php

declare(strict_types=1);

namespace Core\Utility\Services;

final class FormattingService
{
    public static function underscoreToCamelCase(string $underscoreCase): string
    {
        $words = explode('_', strtolower($underscoreCase));

        return $words[0] . implode('', array_map(fn(string $word): string => ucfirst($word), array_slice($words, 1)));
    }

    public static function underscoreToPascalCase(string $underscoreCase): string
    {
        $words = explode('_', strtolower($underscoreCase));

        return implode('', array_map(fn(string $word): string => ucfirst($word), $words));
    }

    public static function underscoreToScreamingSnakeCase(string $underscoreCase): string
    {
        $words = explode('_', $underscoreCase);

        return implode('_', array_map(static fn(string $word): string => strtoupper($word), $words));
    }
}
