<?php

// Suppress error reporting

error_reporting(0);

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Requirements

require_once($_SERVER["DOCUMENT_ROOT"] . "/dir.php");
require_once($REQUIRE_ROUTES);
require_once($REQUIRE_SESSIONS);

// Route Manager

$route_manager = new Route();

// Register Routes

$route_manager->registerRoute(
    aliases: ["", "index", "index.php", "home"],
    route: "home.php"
);

// Resolve Routes

$route = $route_manager->resolveRoute($_SERVER["REQUEST_URI"]);

if ($route !== null) {

    if (isset($route["auth"]) && $route["auth"]) {
        if (!checkAuth()) {
            header("Location: " . $URL_HOME . $route["fallback"]);
            exit();
        }
    }

    require_once($route["path"]);

} else {
    echo "<h1>Error</h1>";
}

?>