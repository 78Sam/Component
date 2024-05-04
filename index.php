<?php

// Requirements

require_once("route.php");
require_once("dir.php");

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
        "fallback"=>"/sales"
    ]
);

// Resolve Routes

$route = $route_manager->resolveRoute($_SERVER["REQUEST_URI"]);

if ($route !== null) {
    require_once($route["path"]);
} else {
    echo "<h1>Error</h1>";
}

?>