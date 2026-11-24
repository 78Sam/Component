<?php

declare(strict_types=1);

namespace Core\CLI;

use Core\Components\Services\ComponentService;
use Core\DependencyInjection\Container;
use Core\Utility\Paths\Models\Path;
use Core\Utility\Paths\Services\PathService;

class CLI
{
    /**
     * @param list<string> $argv
     */
    public function __construct(
        public int $argc,
        public array $argv,
    ) {
    }

    public function main(): void
    {
        if ($this->argc < 2) {
            return;
        }

        $command = strtolower($this->argv[1]);
        match($command) {
            'server' => $this->server(),
            'generate' => $this->generate(),
            default => throw new \Exception("No command '{$command}'"),
        };
    }

    private function server(): void
    {
        exec('cd ComponentPHP/Public && php -S localhost:8080');
    }

    private function generate(): void
    {
        $container = Container::getInstance();
        $componentService = $container->get(ComponentService::class);
        $componentsFolderPath = new Path('App/Components');

        $directoryIterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $componentsFolderPath->getPath(),
                \RecursiveDirectoryIterator::SKIP_DOTS,
            ),
        );

        $components = [];
        /** @var \SplFileInfo $file */
        foreach ($directoryIterator as $file) {
            $filename = PathService::removeExtension($file);
            if (!str_ends_with($filename, '_component')) {
                continue;
            }

            $loadedComponents = $componentService->loadFile(new Path($file->getPathname(), true));
            var_dump($loadedComponents);

            $components[] = $file->getPathname();
        }


    }
}
