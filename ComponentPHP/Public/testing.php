<?php

declare(strict_types=1);

use Core\Components\AbstractTemplate;
use Core\Components\Services\ComponentService;
use Core\Databases\Services\DatabaseService;
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

// $tester = new TestRunner();
// $tester->runAllTests();

$componentService = new ComponentService();
$databaseService = DatabaseService::getInstance();

$path = relativeToAbsolutePath('App/SQL/main.sqlite3');
$databaseService->connect("sqlite:{$path}");

// TODO: Do tests for database stuff but with an in-memory sqlite database sqlite::memory: or something

// $addUserComponent = $componentService->get('add_user', 'App/SQL/users.sql');
// $addUserComponent->fill('user', 'uma');
// print_r($databaseService->query($addUserComponent));

$singleUserComponent = $componentService->get('user', 'App/SQL/users.sql');
$getUserComponent = $componentService->get('get_user', 'App/SQL/users.sql');
$getUserComponent->fill('user', $singleUserComponent->fill('user', 'sam')->fill('user2', 'uma'));
$result = $databaseService->query($getUserComponent);

foreach ($result as $row)
{
    print_r($row);
}

// $getAllUsersComponent = $componentService->get('get_all_users', 'App/SQL/users.sql');
// $result = $databaseService->query($getAllUsersComponent);

// foreach ($result as $row)
// {
//     print_r($row);
// }
