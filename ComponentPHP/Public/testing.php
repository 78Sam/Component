<?php

declare(strict_types=1);

use Core\DependencyInjection\Container;
use Core\Language\Parsers\Components\ComponentParser;
use Core\Language\Tokenizers\ComponentTokenizer;
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

// $tester = $container->get(TestRunner::class);
// $tester->runTestClass(ReflectionTests::class);

// $tester->runAllTests();

// $reflectionService = $container->get(ReflectionService::class);

$tokenizer = $container->get(ComponentTokenizer::class);
$tokens = $tokenizer->tokenize(file_get_contents(PathService::fromProjectDirectory('App/Components/HTML/app.html')));

var_dump($tokens);

$parser = $container->get(ComponentParser::class);
$parser->validate($tokens);
