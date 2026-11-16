<?php

declare(strict_types=1);

use Core\Components\Services\ComponentService;
use Core\DependencyInjection\Container;
use Core\Utility\Paths\Models\Path;
use Core\Utility\Paths\Services\PathService;

/** @var \Composer\Autoload\ClassLoader $classLoader */
$classLoader = require_once dirname(__DIR__) . '/vendor/autoload.php';
$psr4Namespaces = [];
foreach ($classLoader->getPrefixesPsr4() as $namespace => $paths) {
    foreach ($paths as $path) {
        $psr4Namespaces[PathService::normalisePath(realpath($path))] = trim($namespace, '\\');
    }
}
/** @var array<string, string> */
define('PSR4_NAMESPACES', $psr4Namespaces);

$container = Container::getInstance();

$service = $container->get(ComponentService::class);
$comps = $service->loadFile(new Path('App/Components/HTML/test.html'));

// var_dump($comps);
$component = $comps['user'];
$component->fill('username', 'sam');
echo "'" . $component->render() . "'\n";
