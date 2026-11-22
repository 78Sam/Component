<?php

declare(strict_types=1);

use Core\Components\Services\ComponentService;
use Core\DependencyInjection\Container;
use Core\DependencyInjection\Services\ReflectionService;
use Core\Temp\MyAttribute;
use Core\Temp\TempClass;
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

$reflectionService = $container->get(ReflectionService::class);
/** @var \ReflectionClass<TempClass> $p */
$p = new \ReflectionClass(TempClass::class);
$x = $p->getMethods();
var_dump($reflectionService::getAttributesOnClassMethods(
    MyAttribute::class,
    new \ReflectionClass(TempClass::class),
));
