<?php

declare(strict_types=1);

namespace Core\Utility\Services;

final class ClassFinderService
{
    public function __construct(
        public bool $recursive = true,
    ) {}

    /**
     * @template T
     *
     * @param string $path Path relative to the project directory
     * @param class-string<T> $parentClassString
     *
     * @return list<\ReflectionClass<T>>
     */
    public function byExtension(string $path, string $parentClassString): array
    {
        $results = [];
        if (!file_exists($path) || !is_dir($path)) {
            return [];
        }

        $iterator = $this->getIterator($path);
        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            $classString = fileToClassString($file);
            if ($classString === null) {
                continue;
            }

            $reflectionClass = new \ReflectionClass($classString);
            $parentClass = $reflectionClass->getParentClass();
            if ($parentClass === false) {
                continue;
            }

            if ($parentClass->name === $parentClassString) {
                $results[] = $reflectionClass;
            }
        }

        return $results;
    }

    private function getIterator(string $path): \RecursiveCallbackFilterIterator|\RecursiveIteratorIterator
    {
        $directoryIterator = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);

        $filterIterator = new \RecursiveCallbackFilterIterator($directoryIterator, function (
            \SplFileInfo $file,
            string $_key,
            \RecursiveDirectoryIterator $iterator,
        ) {
            if ($iterator->hasChildren() && $this->recursive) {
                return true;
            }

            return $file->getExtension() === 'php';
        });

        if (!$this->recursive) {
            return $filterIterator;
        }

        return new \RecursiveIteratorIterator($filterIterator);
    }
}
