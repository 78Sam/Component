<?php

declare(strict_types=1);

use Core\Components\AbstractTemplate;
use Core\Components\Services\ComponentService;
use Core\Routing\Router;
use Core\Testing\AbstractTest;
use Core\Testing\TestRunner;
use Core\Utility\ClassFinder;
use Core\Utility\Validators\Services\ValidatorService;
use Core\Utility\Validators\Types\StringValidator;

/** @var \Composer\Autoload\ClassLoader $classLoader */
$classLoader = require_once dirname(__DIR__) . '/vendor/autoload.php';
$psr4Namespaces = [];
foreach ($classLoader->getPrefixesPsr4() as $namespace => $paths) {
    foreach ($paths as $path) {
        $psr4Namespaces[normalisePath(realpath($path))] = trim($namespace, '\\');
    }
}
/** @var array<string, string> */
define('PSR4_NAMESPACES', $psr4Namespaces);

$tester = new TestRunner();
$tester->runAllTests();

// $componentService = new ComponentService();
// $componentService->loadFile('Tests/Core/Components/Include/Components/Complex.html');

// $router = new Router();
// $router->createSiteMap();
// print_r($router->siteMapEntries);

// class TestTemplate extends AbstractTemplate
// {
//     public function __construct()
//     {
//         $this->loadFile('test.html');
//     }
// }

// $x = new TestTemplate();
// $component = $x->get('test_component');
// $component->fill('myVar', 'hi there');
// $component->fill('newVar', 'hi there 2');
// echo $component->__toString();