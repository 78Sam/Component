<?php

// Suppress error reporting

// error_reporting(0);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Requirements

// $path = "";
// while (!file_exists($path . "dir.php")) {
//     $path = $path . "../";
// }
// require_once($path . "dir.php");
require_once(__DIR__ . "/dir.php");
require_once($REQUIRE_ROUTES);
require_once($REQUIRE_SESSIONS);

// Route Manager

$route_manager = new Route();

// Register Routes

$route_manager->registerRoute(
    aliases: ["", "index", "home"],
    route: "home.php"
);

$route_manager->registerRoute(
    aliases: ["login", "signin", "sign%20in"],
    route: "login.php"
);

$route_manager->registerRoute(
    aliases: ["staff", "tryme"],
    route: "staff.php",
    options: [
        "auth"=>true,
        "fallback"=>"login"
    ]
);

// Resolve Routes

$route = $route_manager->resolveRoute($_SERVER["REQUEST_URI"]);

// print_r($route);

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