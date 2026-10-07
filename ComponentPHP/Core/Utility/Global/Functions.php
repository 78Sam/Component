<?php

declare(strict_types=1);

use Core\Debug\DebugMetrics;
use Core\Utility\Services\PathService;

if (!function_exists('dump')) {
    function dump(mixed ...$values): void
    {
        $debugFrame = DebugMetrics::getBacktrace();

        echo "<pre>{$debugFrame}\n\n";
        /** @mago-expect analysis:mixed-assignment */
        foreach ($values as $value) {
            $data = var_export($value, return: true);
            echo htmlspecialchars($data);
            echo '<br>';
        }
        echo "\n\n</pre>";
    }
}

/**
 * @return ?class-string
 */
function fileToClassString(\SplFileInfo $file): ?string
{
    if ($file->getExtension() !== 'php') {
        // throw new \Exception("Can only convert php files to class-strings, not '{$file->getRealPath()}'");
        return null;
    }

    $realPath = $file->getRealPath();
    if ($realPath === false) {
        return null;
    }

    $filePath = substr(PathService::normalisePath($realPath), 0, -4);
    foreach (PSR4_NAMESPACES as $path => $namespace) {
        if (str_contains($filePath, $path)) {
            $classString = str_replace('/', '\\', str_replace($path, $namespace, $filePath));
            if (!class_exists($classString)) {
                continue;
            }

            return $classString;
        }
    }

    return null;
}
