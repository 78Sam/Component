<?php

// Suppress error reporting

error_reporting(0);

// Requirements

require_once("dir.php");
require_once("route.php");
require_once("auth.php");

// Route Registration

$route_manager = new Route();

$route_manager->registerRoute(
    aliases: ["", "index", "index.php", "home"],
    route: "home.php"
);

$route_manager->registerRoute(
    aliases: ["sales"],
    route: "sales.php"
);

$route_manager->registerRoute(
    aliases: ["products/iphone"],
    route: "products/iphone.php",
    options: [
        "auth"=>true,
        "fallback"=>"/loginpage"
    ]
);

$route_manager->registerRoute(
    aliases: ["login", "loginpage"],
    route: "loginpage.php"
);

// Resolve Routes

$route = $route_manager->resolveRoute($_SERVER["REQUEST_URI"]);

if ($route !== null) {

    if (isset($route["auth"]) && $route["auth"]) {
        if (!checkAuth()) {
            header("Location: https://sam-mccormack.co.uk/Test" . $route["fallback"]);
            exit();
        }
    }

    require_once($route["path"]);

} else {
    echo "<h1>Error</h1>";
}

?>