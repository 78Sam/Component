<?php

declare(strict_types=1);

use Core\Components\Services\ComponentService2;
use Core\DependencyInjection\Container;
use Core\Language\Parsers\Components\ComponentParser;
use Core\Language\Tokenizers\Components\ComponentTokenizer;
use Core\Tests\TestRunner;
use Core\Utility\Services\PathService;
use Tests\Core\Reflection\ReflectionTests;

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

$service = $container->get(ComponentService2::class);
$comps = $service->loadFile(
    PathService::fromProjectDirectory('App/Components/HTML/test.html'),
    true,
);

// var_dump($comps);
$component = $comps['user'];
$component->fill('username', 'sam');
echo "'" . $component->render() . "'\n";
