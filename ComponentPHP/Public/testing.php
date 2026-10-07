<?php

declare(strict_types=1);

use Core\Tests\TestRunner;
use Core\Utility\Services\PathService;
use Tests\Core\Database\DatabaseTests;

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

$tester = new TestRunner();
$tester->runAllTests();

// $tester->runTestClass(DatabaseTests::class);
