<?php

declare(strict_types=1);

use Core\Kernel;
use Core\Utility\Services\PathService;

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

$kernel = new Kernel(bin2hex(random_bytes(4)));
$kernel->boot();

$handler = static function () use ($kernel) {
    try {
        $kernel->handleRequest($_SERVER, $_GET, $_POST, $_FILES, $_COOKIE);
    } catch (\Throwable $exception) {
        dump($exception);
    }
};

if (($_SERVER['SERVER_SOFTWARE'] ?? null) === 'FrankenPHP') {
    $totalRequests = 0;
    while (true) {
        $keepRunning = frankenphp_handle_request($handler);

        gc_collect_cycles();

        if (!$keepRunning) {
            break;
        }
    }
} else {
    $handler();
}

$kernel->shutdown();
