<?php

declare(strict_types=1);

namespace Core\Utility\Services;

final class FormattingService
{
    public static function underscoreToPascalCase(string $underscoreCase): string
    {
        $words = explode('_', $underscoreCase);

        return $words[0] . implode('', array_map(fn(string $word): string => ucfirst($word), array_slice($words, 1)));
    }
}
