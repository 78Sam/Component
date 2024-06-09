<?php

// For testing only

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Suppress error reporting

error_reporting(0);

// Base requirements

require_once(__DIR__ . "/../dir.php");
require_once($REQUIRE_DATABASE);
require_once($REQUIRE_ROUTES);

// Middleware requirements

// require_once($MIDDLEWARE_FOLDER . "yourmiddleware.php");

// Routes

$home = new Route(
	aliases: ["/", "/home"],
	path: "home.php",
	middleware: []
);

// route-placeholder

$routes = [
	$home,
	// routes-placeholder
];

foreach ($routes as $route) {
    if ($route->isRoute($_SERVER["REQUEST_URI"])) {
        $path = __DIR__ . "/../views/" . $route->use();
        include_once($path);
    }
}

?>