<?php

declare(strict_types=1);

namespace Core\CLI;

use Core\Components\Services\ComponentService;
use Core\DependencyInjection\Container;
use Core\Internals\Templates\SchematicTemplate;
use Core\Utility\Paths\Models\Path;
use Core\Utility\Paths\Services\PathService;
use Core\Utility\Services\FormattingService;

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
            'test' => $this->test(),
            default => throw new \Exception("No command '{$command}'"),
        };
    }

    private function server(): void
    {
        exec('cd ComponentPHP/Public && php -S 0.0.0.0:8080');
    }

    private function generate(): void
    {
        $container = Container::getInstance();

        $componentsFolderPath = new Path('App/Components');
        $directoryIterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $componentsFolderPath->getPath(),
                \RecursiveDirectoryIterator::SKIP_DOTS,
            ),
        );

        $componentService = $container->get(ComponentService::class);
        $schematicTemplate = $container->get(SchematicTemplate::class);

        /** @var \SplFileInfo $file */
        foreach ($directoryIterator as $file) {
            $path = new Path($file->getPathname(), true);
            $filename = FormattingService::underscoreToPascalCase($path->getFilename()) . "Schematic";

            $loadedComponents = $componentService->loadFile($path);
            $schematic = $schematicTemplate->getSchematic($loadedComponents, $path);

            $schematicsPath = str_replace('App/Components', 'App/Schematics', PathService::makeRelative($path->getPath()));
            $fullSchematicsPath = dirname(PathService::fromProjectDirectory($schematicsPath)) . "/{$filename}.php";

            // file_put_contents("{$filename}.php", )

            if (!is_dir(dirname($fullSchematicsPath)) && !file_exists(dirname($fullSchematicsPath)))
            {
                mkdir(dirname($fullSchematicsPath), recursive: true);
            }

            file_put_contents($fullSchematicsPath, $schematic->render());
        }


    }

    private function test(): void
    {
        echo PathService::makeRelative('/home/sam/Component/ComponentPHP/Core/Internals/Components/schematic.php');
    }
}
